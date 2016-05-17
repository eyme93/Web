<?php

namespace SamGunBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use SamGunBundle\Entity\Forum\Categories;
use SamGunBundle\Entity\Forum\SousCategories;
use SamGunBundle\Entity\Forum\Message;
use SamGunBundle\Entity\Forum\Topics;
use SamGunBundle\Entity\Forum\Topics_Categories;
use SamGunBundle\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Validator\Constraints\DateTime;

class ForumController extends Controller
{

  /**
  * @Route("/forum" ,name ="forum_index")
  */
  public function indexForumAction(){
    $repository = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Categories');
    $Categories = $repository->findAll();
    $repository2 = $this->getDoctrine()->getRepository('SamGunBundle:Forum\SousCategories');
    $SousCategories = $repository2->findAll();

    $nb_array = array();
    $repoTopics = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Topics');
    $repoMessage = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Message');
    for($i=0;$i<sizeof($Categories);$i++){
      $messbycat = $repoMessage->getbymessage($Categories[$i]->getId());
      $topicsbycat = $repoTopics->getnbTopicsbycat($Categories[$i]->getId());
      $mess = $messbycat + $topicsbycat;
      $c = (object) array('id_categorie'=> $Categories[$i]->getId() ,'nb_messages'=>$mess);
      array_push($nb_array,$c);
    }
    //var_dump($nb_array);

    return $this->render('SamGunBundle:Forum:index.html.twig',array( 'Categories' => $Categories,'SousCategories' => $SousCategories,'Mess' => $nb_array ));

  }

  /**
  * @Route("/forum/topic/nouveau" ,name ="forum_new_topic")
  */
  public function createTopicAction(Request $request){
    if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
      throw $this->createAccessDeniedException();
    }
     $user = $this->getUser();

     $id_categorie  = $request->get('categorie');
     if($id_categorie){
       $id_categorie = intval($id_categorie);
       //print_r('OK');
     }

     $repository = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Categories');
     $categorie=$repository->findOneById($id_categorie);

     $repository2 = $this->getDoctrine()->getRepository('SamGunBundle:Forum\SousCategories');
     $Souscategorie=$repository2->findByidCategorie($id_categorie);

     $Souscategorie_name = [];
     for($i=0;$i<sizeof($Souscategorie);$i++){
       //array_push($Souscategorie_name,$Souscategorie[$i]->getId());
       $Souscategorie_name[$Souscategorie[$i]->getNom()]=$Souscategorie[$i]->getId();
       //print_r($Souscategorie_name[$i]);
     }
     //$request->request->get('page');
    $topic= new Topics();
    $topiccat = new Topics_Categories();

    $c = (object) array('titre' => "", 'contenu'=> "",'id_categorie'=>$id_categorie,'id_SousCategories'=> 0);

    $form = $this->createFormBuilder($c)
    ->add('titre', TextType::class)
    ->add('contenu', TextareaType::class)
    ->add('id_SousCategories', ChoiceType::class,array(
      'choices'=> $Souscategorie_name
    ))
    ->add('save', SubmitType::class)
    ->getForm();

    /*$form = $this->createFormBuilder($topic)
    ->add('titre',    TextType::class)
    ->add('contenu',      TextareaType::class)
    ->add('save',      SubmitType::class)
    ->getForm();*/

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      //print_r($c->id_SousCategories);
      $topic->setTitre($c->titre);
      $topic->setContenu($c->contenu);
      $date = new \DateTime('now');
      $topic->setDateHeure($date);
      $topic->setIdCreateur($user->getId());
      $em = $this->getDoctrine()->getManager();
      $em->persist($topic);
      $em->flush();

      $repository3 = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Topics');
      $created_topic=$repository3->findOneByDateHeure($date);

      $topiccat->setIdTopic($created_topic->getId());
      $topiccat->setIdCategorie($id_categorie);
      $topiccat->setIdSouscategorie($c->id_SousCategories);


      $em->persist($topiccat);
      $em->flush();


      return $this->redirectToRoute('forum_index');
    }

    return $this->render('SamGunBundle:Forum:formulaire_topic.html.twig',array( 'form' => $form->createView() , 'CatName' => $categorie->getNom(), 'Souscat' => $Souscategorie));

  }


  /**
  * @Route("/forum/topics" ,name ="forum_topics" )
  */
  public function afficheTopicsAction(Request $request){
    $Result ;
    $c;
    $get_categorie = $request->query->get('categorie');
    $get_souscategorie = $request->query->get('sous_categorie');
    if($get_categorie){
    }else{
      die("Il manque la catégorie");
    }

    $repoCategorie = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Categories');
    $id_categories = $repoCategorie->get_id_by_name($get_categorie);

    $repoTopics = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Topics');




    if($get_souscategorie){
      $repoSousCategorie = $this->getDoctrine()->getRepository('SamGunBundle:Forum\SousCategories');
      $id_SousCategories = $repoSousCategorie->get_id_by_name($get_souscategorie);
      $Result = $repoTopics->getTopics($id_categories,$id_SousCategories);
      //$c = (object) array('id_categorie' => $id_categories,'id_souscategorie' => $id_SousCategories);
    }else{
      $Result = $repoTopics->getTopics_by_cat($id_categories);
      //$c = (object) array('id_categorie' => $id_categories);
    }
    $topics = array();
    $user = array();
    $length = sizeof($Result);

    for($i=0;$i<$length;$i++){
      if ($Result[$i] instanceof Topics){
        array_push($topics,$Result[$i]);
      }else{
        array_push($user,$Result[$i]);
      }
    }

    $test = $id_categories[0];
    //$c = (object) array('id_categorie' => $id_categories,'id_souscategorie' => $id_souscategorie);
    //print_r($test);

    $repoMessage = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Message');
    $nb_array = array();
    for($i=0;$i<sizeof($topics);$i++){
      $messbytopic = $repoMessage->getnbmessbyTopic($topics[$i]);
      $mess = $messbytopic + 1;

      $last_m = $repoMessage->getLastMessagebyId($topics[$i]);
      $date ;
      if($last_m){
        $date = $last_m[0]->getDateHeurePost();
        $c = (object) array('id_topic'=> $topics[$i]->getId() ,'nb_messages'=>$mess ,'last_m' => $date);
      }else{
        $tt  = "00-00-0000";
        $date = \DateTime::createFromFormat('d-m-Y',$tt);
        $c = (object) array('id_topic'=> $topics[$i]->getId() ,'nb_messages'=>$mess ,'last_m' => $date);

      }
      array_push($nb_array,$c);
    }

    return $this->render('SamGunBundle:Forum:topics_view.html.twig',array('Topics' => $topics , 'User' => $user ,'categorie' => $test['id'] ,'Mess' => $nb_array) );
  }



  /**
  * @Route("/forum/topic/article/{id}", name="forum_article" )
  */
  public function afficheArticleAction(Request $request,$id){
    if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
      throw $this->createAccessDeniedException();
    }

    $repoTopics = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Topics');
    $Topic = $repoTopics->findOneById($id);

    $repoMessage = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Message');
    $AllMessage = $repoMessage->findByIdTopic($id);

    $c_tab = array();

    $repoTopics = $this->getDoctrine()->getRepository('SamGunBundle:User');
    $user = $repoTopics->findOneById($Topic->getIdCreateur());
    //$user2 = $repoTopics->findAll();

    for($i=0;$i<sizeof($AllMessage);$i++){
      $user2 = $repoTopics->findOneById($AllMessage[$i]->getIdPosteur());
      //print_r($user2);
      $c = (object) array('contenu'=> $AllMessage[$i]->getContenu() ,'id_posteur'=>$user2->getUsername());
      array_push($c_tab,$c);
    }


    $user = $this->getUser();

    //var_dump($Topic);


    $message = new Message();

    $form = $this->createFormBuilder($message)
    ->add('contenu',      TextareaType::class)
    ->add('save',      SubmitType::class)
    ->getForm();


    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $message->setIdTopic($Topic->getId());
      $date = new \DateTime('now');
      $message->setDateHeurePost($date);
      //print_r("Debut ");
      //print_r($user->getId());
      $message->setIdPosteur($user->getId());
      $em = $this->getDoctrine()->getManager();
      $em->persist($message);
      $em->flush();

      $repoMessage = $this->getDoctrine()->getRepository('SamGunBundle:Forum\Message');
      $AllMessage = $repoMessage->findByIdTopic($id);

      $c_tab = array();

      $repoTopics = $this->getDoctrine()->getRepository('SamGunBundle:User');
      $user = $repoTopics->findOneById($Topic->getIdCreateur());
      //$user2 = $repoTopics->findAll();

      for($i=0;$i<sizeof($AllMessage);$i++){
        $user2 = $repoTopics->findOneById($AllMessage[$i]->getIdPosteur());
        $c = (object) array('contenu'=> $AllMessage[$i]->getContenu() ,'id_posteur'=>$user2->getUsername());
        array_push($c_tab,$c);
      }

      return $this->render('SamGunBundle:Forum:article.html.twig',array( 'form'=>$form->createView() ,'topic' => $Topic,'user' => $user,'Message'=>$c_tab ));
    }

    return $this->render('SamGunBundle:Forum:article.html.twig',array( 'form'=>$form->createView() ,'topic' => $Topic,'user' => $user,'Message'=>$c_tab ));
  }
}



 ?>
