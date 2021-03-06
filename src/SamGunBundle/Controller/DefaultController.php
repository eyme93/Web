<?php

namespace SamGunBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use AppBundle\Entity\Product;
use SamGunBundle\Entity\Demande;
use SamGunBundle\Entity\Formation;
use SamGunBundle\Entity\Salarie;
use SamGunBundle\Entity\Candidature;
use SamGunBundle\Entity\Poste;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



use \DateTime;

class DefaultController extends Controller{
  /**
  * @Route("/" ,name ="home")
  */
  public function indexAction(){
    /*$repository = $this->getDoctrine()->getRepository('SamGunBundle:Salarie');
    $tab_salarie = $repository->findAll();

    $em = $this->getDoctrine()->getManager();
    $salarieRepo = $em->getRepository('SamGunBundle:Salarie');

    for($i =1500 ;$i<2001 ;$i++){
      $nb = $salarieRepo->get_role_nb($tab_salarie[$i]->getId());
      $user = new User();
      $user ->setId($tab_salarie[$i]->getId());
      $user -> setUsername($tab_salarie[$i]->getNom());
      $user -> setPassword($tab_salarie[$i]->getPrenom());
      if($nb == 0){
        $user -> setRole('ROLE_USER');
      }else{
        $user -> setRole('ROLE_ADMIN');
      }
      $user-> setIsActive(false);

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();
    }*/

    return $this->render('SamGunBundle:Default:index.html.twig');
  }

  /**
  * @Route("/stat/", name="stat_index")
  */
  public function statAction(Request $request){
    $c = (object) array(
      'pfh' => 0, 'pcdi'=> 0, 'pcdd'=> 0,'pcs' => 0,
      'ps2' => 0, 'ps5'=> 0, 'ps10'=> 0,
      'eff1_annee'=>false,'eff2_annee'=> null
    );

    $form = $this->createFormBuilder($c)
    ->add('pfh', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage Femmes/Hommes"))
    ->add('pcdi', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage CDI"))
    ->add('pcdd', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage CDD"))
    ->add('pcs', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage Stagiaire"))
    ->add('ps2', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage salaire > 2000"))
    ->add('ps5', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage salaire > 5000"))
    ->add('ps10', ChoiceType::class,
    array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label'=>"Pourcentage salaire > 10000"))
    ->add('eff1_annee',ChoiceType::class,array ('choices' => array ('Yes' => 1, 'No' => 0),'expanded' => true,'label' => "Nombre de Salariés" ))
    ->add('eff2_annee', DateType::class, array ('placeholder' => 'Select a value','label' => "Si oui,depuis :"))
    ->add('save', SubmitType::class, array('label' => 'OK'))
    ->getForm();

    $url = "";

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      if($c->pfh == 1){
        $url = "pfh";
      }
      if($c->pcdd == 1 ){
        $url = $url." "."pcdd";
      }
      if($c->pcdi == 1 ){
        $url = $url." "."pcdi";
      }
      if($c->pcs == 1 ){
        $url = $url." "."pcs";
      }
      if($c->ps2 == 1 ){
        $url = $url." "."ps2";
      }
      if($c->ps5 == 1 ){
        $url = $url." "."ps5";
      }
      if($c->ps10 == 1 ){
        $url = $url." "."ps10";
      }
      if($c->eff1_annee == 1){
        $jour = $c->eff2_annee->format('w');
        $mois = $c->eff2_annee->format('m');
        $annee = $c->eff2_annee->format('y');
        $url= $url." "."eff1_annee"." ".$annee."-".$mois."-".$jour;
      }


      if(empty($url)){
        $url = "vide";
      }
      return $this->redirectToRoute('test_index', array('url' =>$url));
    }
    return $this->render('SamGunBundle:Stat:index.html.twig', array('form' => $form->createView()));
  }

  /**
  * @Route("/stat/{url}", name="test_index")
  */
  public function testAction($url){
    if(strcmp($url,"vide")=== 0){
      $tab1 = array();
      return $this->render('SamGunBundle:Stat:stat_test.html.twig',array("tableau1" => $tab1));
    }else{
      $em = $this->getDoctrine()->getManager();
      $salarieRepo = $em->getRepository('SamGunBundle:Salarie');
      $split = preg_split('/ /',$url);
      $nb = $salarieRepo->get_nb_salaries();
      $nb_femmes = 0;
      $nb_handi = 0;
      $tab1 = array();
      if(array_search('pfh',$split)!==false){
        $nb_femmes = $salarieRepo->get_nb_femmes();
        $nb_femmes = ($nb_femmes / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage Femmes", 'value'=> $nb_femmes ,'p' => true );
        $c2 = (object) array('title' => "Pourcentage Hommes", 'value'=> 100-$nb_femmes,'p' => true );
        $tab1[] = $c1;
        $tab1[] = $c2;
      }
      if(array_search('pcdd',$split)!==false){
        $cdd = "CDD";
        $nb_cdd = $salarieRepo->get_nb_employe($cdd);
        $nb_cdd= ($nb_cdd / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage CDD", 'value'=> $nb_cdd,'p' => true );
        $tab1[] = $c1;
      }
      if(array_search('pcdi',$split)!==false){
        $cdi = "CDI";
        $nb_cdi = $salarieRepo->get_nb_employe($cdi);
        $nb_cdi= ($nb_cdi / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage CDI", 'value'=> $nb_cdi,'p' => true );
        $tab1[] = $c1;
      }
      if(array_search('pcs',$split)!==false){
        $sta = "Sta";
        $nb_sta = $salarieRepo->get_nb_employe($sta);
        $nb_sta= ($nb_sta / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage Stagiaire", 'value'=> $nb_sta,'p' => true );
        $tab1[] = $c1;
      }
      if(array_search('ps2',$split)!==false){
        $salaire = 2000;
        $nb_salarie = $salarieRepo->get_nb_employe_salaire($salaire);
        $nb_salarie= ($nb_salarie / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage Employé >2000", 'value'=> $nb_salarie,'p' => true );
        $tab1[] = $c1;
      }
      if(array_search('ps2',$split)!==false){
        $salaire = 5000;
        $nb_salarie = $salarieRepo->get_nb_employe_salaire($salaire);
        $nb_salarie= ($nb_salarie / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage Employé >5000", 'value'=> $nb_salarie,'p' => true );
        $tab1[] = $c1;
      }
      if(array_search('ps2',$split)!==false){
        $salaire = 10000;
        $nb_salarie = $salarieRepo->get_nb_employe_salaire($salaire);
        $nb_salarie= ($nb_salarie / $nb)*100 ;
        $c1 = (object) array('title' => "Pourcentage Employé >10000", 'value'=> $nb_salarie,'p' => true );
        $tab1[] = $c1;
      }
      if(($k=array_search('eff1_annee',$split))!==false){
        $date = $split[$k+1];
        $tab = preg_split('/-/',$date);
        $tab[0] = 2000+$tab[0];
        $date = $tab[0]."-".$tab[1]."-".$tab[2];
        //$annee = new DateTime($nb_annees.'-01-01');
        $nb_annees = $salarieRepo->get_salarie_annee($date);
        $c1 = (object) array('title' => "Nombre de salarié dans l'entreprise au jour du: ".$date, 'value'=> $nb_annees,'p' => false );
        $tab1[] = $c1;
      }

      return $this->render('SamGunBundle:Stat:stat_test.html.twig',array("tableau1" => $tab1));
    }
  }


  /**
    * @Route("/homepag",name="homepag")
    */
    public function afficheAction() {
      $repository = $this->getDoctrine()->getRepository('SamGunBundle:Formation');
      $formation = $repository->findAll();
      $repository2 = $this->getDoctrine()->getRepository('SamGunBundle:Demande');
      $demande = $repository2->findAll();

      //return $this->render('SamGunBundle:Default:formation.html.twig');
      return $this->render('SamGunBundle:Default:formation.html.twig',array( 'form' => $formation,'dem' => $demande ));
    }
    /**
    * @Route("/poste",name="poste")
    */
    public function afficheposteAction() {
      $repository = $this->getDoctrine()->getRepository('SamGunBundle:Poste');
      $poste= $repository->findAll();

      //return $this->render('SamGunBundle:Default:formation.html.twig');
      return $this->render('SamGunBundle:Default:poste.html.twig',array( 'poste' => $poste));

    }

    /**
    * @Route("/validation1",name="validation1")
    */
    public function valideCandidatureAction() {
      $repository = $this->getDoctrine()->getRepository('SamGunBundle:Candidature');
      $formation = $repository->findAll();
      $repository2 = $this->getDoctrine()->getRepository('SamGunBundle:Poste');
      $poste = $repository2->findAll();
      //return $this->render('SamGunBundle:Default:formation.html.twig');
      return $this->render('SamGunBundle:Default:candidature.html.twig',array( 'cand' => $formation ,'poste' => $poste));

    }
      /**
      * @Route("/validate",name="validate")
      */
      public function valideAficheAction() {
        $repository = $this->getDoctrine()->getRepository('SamGunBundle:Formation');
        $formation = $repository->findAll();
        $repository2 = $this->getDoctrine()->getRepository('SamGunBundle:Demande');
        $demande = $repository2->findAll();
        $repository3 = $this->getDoctrine()->getRepository('SamGunBundle:Salarie');
        $salarie= $repository3->findAll();
        //return $this->render('SamGunBundle:Default:formation.html.twig');
        return $this->render('SamGunBundle:Default:validatorformation.html.twig',array( 'form' => $formation,'dem' => $demande,'salarie' => $salarie ));

      }
      /**
      * @Route("/validate/{count}//{count2}/",name="validat")
      */
      public function valideAction($count,$count2) {
        $repository = $this->getDoctrine()->getRepository('SamGunBundle:Demande');
        $demande=$repository->findOneById($count);
        $demande->setStatus($count2);

        //return $this->render('SamGunBundle:Default:formation.html.twig');
        return $this->render('SamGunBundle:Default:formation.html.twig');

      }

    /**
    * @Route("/homepag/{count}/",name="myform")
    */
    public function afficheDemande($count) {

      $repository = $this->getDoctrine()->getRepository('SamGunBundle:Demande');
      $demande=$repository->findOneById($count);
      //return $this->render('SamGunBundle:Default:formation.html.twig');
      return $this->render('SamGunBundle:Default:formation.html.twig',array( 'form' => $demande ));

    }
    /**
    * @Route("/admi",name="admi")
    */
    public function afficheadmi() {

      $repository = $this->getDoctrine()->getRepository('SamGunBundle:Candidature');
      $demande=$repository->findAll();
      //return $this->render('SamGunBundle:Default:formation.html.twig');
      return $this->render('SamGunBundle:Default:admi.html.twig',array( 'cand' => $demande));

    }


    /**
    * @Route("/envoye/{count}",name="envoyer")
    */
    public function envoyer($count) {
      $em = $this->getDoctrine()->getManager();
      $demande = $em->getRepository('SamGunBundle:Candidature')->find($count);
      $demande->setRemarque( $_POST["pseudo"]);
      $em->flush();
      return $this->render('SamGunBundle:Default:index.html.twig');
    //  return $this->render('SamGunBundle:Default:formation.html.twig',array( 'form' => $demande ));
    }

    /**
    * @Route("/inscription/{count}/{count2}",name="inscription")
    */

    public function createDemande($count,$count2) {
          $em = $this->getDoctrine()->getManager();
          $demande = $em->getRepository('SamGunBundle:Demande')->find($count2);
          $demande->setStatus( $count);
          $em->flush();
      //return $this->render('SamGunBundle:Default:formation.html.twig');
      return $this->render('SamGunBundle:Default:index.html.twig');

    }



  /**
  * @Route("/formation/",name="forma")
  */
  public function formation_Formulaire(Request $request){
    // On crée un objet Advert
    $formation= new Formation();
    // On crée le FormBuilder grâce au service form factory
    //$formBuilder = $this->get('form.factory')->createBuilder('form',   $formation);
    $form = $this->createFormBuilder($formation)
    ->add('date',     DateType::class)
    ->add('nomFormation',    TextType::class)
    ->add('contenu',      TextareaType::class)
    ->add('prerequis',     TextType::class)
    ->add('duree',  TextType::class)
    ->add('cout',  TextType::class)
    ->add('save',      SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)
    if ($form->isValid()) {
      // On l'enregistre notre objet $advert dans la base de données, par exemple
      $em = $this->getDoctrine()->getManager();
      $em->persist($formation);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirect($this->generateUrl('forma', array('id' => $formation->getId())));
    }
    // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

    // À partir du formBuilder, on génère le formulaire
    //$form = $formBuilder

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('SamGunBundle:Default:createformation.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  /**
  * @Route("/fposte/{count}",name="fposte")
  */
  public function Poste_Formulaire(Request $request,$count){
    // On crée un objet Advert){
    $formation= new Poste();
    // On crée le FormBuilder grâce au service form factory
    //$formBuilder = $this->get('form.factory')->createBuilder('form',   $formation);
    $form = $this->createFormBuilder($formation)
    ->add('metier',    TextType::class)
    ->add('description',      TextareaType::class)
    ->add('save',      SubmitType::class)
    ->getForm();
    $formation->setGestionnaire($count);
    $form->handleRequest($request);
    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)
    if ($form->isSubmitted() && $form->isValid()) {
      // On l'enregistre notre objet $advert dans la base de données, par exemple
      $em = $this->getDoctrine()->getManager();
      $em->persist($formation);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirectToRoute('home');
    }
    // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

    // À partir du formBuilder, on génère le formulaire
    //$form = $formBuilder

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('SamGunBundle:Default:createposte.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  /**
  * @Route("/candidature/{count}",name="candi")
  */
  public function Candi_Formulaire($count,Request $request){
    // On crée un objet Advert
    $formation= new Candidature();
    // On crée le FormBuilder grâce au service form factory
    //$formBuilder = $this->get('form.factory')->createBuilder('form',   $formation);
    $form = $this->createFormBuilder($formation)
    ->add('nom',    TextType::class)
    ->add('prenom',    TextType::class)
    ->add('mail',    TextType::class)
    ->add('diplome',      TextareaType::class)
    ->add('motivation',      TextareaType::class)
    ->add('save',      SubmitType::class)
    ->getForm();
    $formation->setNomduposte($count);
    $form->handleRequest($request);
    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)
    if ($form->isValid()) {
      // On l'enregistre notre objet $advert dans la base de données, par exemple
      $em = $this->getDoctrine()->getManager();
      $em->persist($formation);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      // On redirige vers la page de visualisation de l'annonce nouvellement créée
        return $this->redirectToRoute('home');
    }
    // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard

    // À partir du formBuilder, on génère le formulaire
    //$form = $formBuilder

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('SamGunBundle:Default:createcandidat.html.twig', array(
      'form' => $form->createView(),
    ));
  }


}
