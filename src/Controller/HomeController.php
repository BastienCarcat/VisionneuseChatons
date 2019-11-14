<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $finder=new Finder();
        $finder->directories()->in("../public/photos");

        $form=  $this->createFormBuilder()
            ->add('Nom', TextType::class)
            ->add('Ajouter', SubmitType::class, ['label'=>'Ajouter',
                'attr'=> ["class" => 'btn btn-primary']])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $data = $form->getData();
            mkdir('../public/photos/'.$data["Nom"], 0700);
        }

        return $this->render('home/index.html.twig', [
            "dossiers" => $finder,
            "formIndex" => $form->createView(),
        ]);
    }

    /**
     * @Route("/chatons/{nomDuDossier}", name="dossier")
     */
    public function afficherDossier($nomDuDossier, Request $request)
    {
        $finder=new Finder();
        $finder->files()->in("../public/photos/".urldecode($nomDuDossier));


        //création d'un formulaire
        $form = $this->createFormBuilder()
            ->add('photo', FileType::class, ['label' => 'Ajouter un chaton'])
            ->add('ajouter', SubmitType::class, ['label'=>'Envoyer',
                                                        'attr'=>['class'=>'btn btn-primary']])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();


            $maPhoto = $data["photo"]->getClientOriginalName();
            $i=0;
            $items = scandir("../public/photos/".urldecode($nomDuDossier));
            $maPhotoActuel=$maPhoto;
            foreach ($items as $item) {
                if($maPhoto==$item){
                    $i++;
                    $maPhoto = $maPhotoActuel."(".$i.")";
                }
            }


            $data["photo"]->move("../public/photos/".urldecode($nomDuDossier),
                                                                $maPhoto);
        }
        //1) modifier le code au dessus pour pouvoir ajouter un fichier qui a le même nom sans écraser l'ancien
        //2) sur la page accueil : créer un formulaire qui permet d'ajouter un nouveau dossier (autre que minions etc
        //3) pouvoir supprimer un dossier ou/et supprimer un chaton

        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier" => $nomDuDossier,
            "fichiers" => $finder,
            "formulaire" => $form->createView(),
        ]);
    }

}
