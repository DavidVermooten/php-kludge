<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Entity\CharGenerator;
use AppBundle\Entity\Directory;
use Symfony\Component\Finder\Finder;
use Enzim\Lib\TikaWrapper\TikaWrapper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class TodoController extends Controller{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction(){
        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findAll();

        return $this->render('todo/index.html.twig', array(
            'todos' => $todos
        ));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request){
        $todo = new Todo;

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Create Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Get Data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice','Todo Added'
            );

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request){
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

            $now = new\DateTime('now');

            $todo->setName($todo->getName());
            $todo->setCategory($todo->getCategory());
            $todo->setDescription($todo->getDescription());
            $todo->setPriority($todo->getPriority());
            $todo->setDueDate($todo->getDueDate());
            $todo->setCreateDate($now);

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Edit Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Get Data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em->flush();

            $this->addFlash(
                'notice','Todo Updated'
            );

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/edit.html.twig', array(
            'todo' => $todo,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/edit/", name="todo_edit_redirect")
     */
    public function editRedirectAction(){
        $this->addFlash(
            'error','EDIT ERROR - No ID provided'
        );

        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id){
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

        return $this->render('todo/details.html.twig', array(
            'todo' => $todo
        ));

    }

    /**
     * @Route("/todo/details/", name="todo_details_redirect")
     */
    public function detailsRedirectAction(){
        $this->addFlash(
            'error','VIEW ERROR - No ID provided'
        );

        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);

        $em->remove($todo);
        $em->flush();

            $this->addFlash(
                'notice','Todo Deleted'
            );

        return $this->redirectToRoute('todo_list');
    }

// -------------------------- NEW ACTIONS ----------------------------------
// ------------------------- LUCKY  NUMBER ---------------------------------

    /**
     * @Route("/todo/lucky/", name="todo_lucky")
     */
    public function luckyAction(){
        $number = mt_rand(0,100);

        return $this->render('todo/lucky.html.twig', 
            array(
                'number' => $number
            ));
    }

// ---------------------- CHARACTER GENERATOR ------------------------------

    /**
     * @Route("/todo/generate/", name="todo_generate")
     */
    public function generateAction(Request $request){
        $chargenerator = new CharGenerator();

        $form = $this->createFormBuilder($chargenerator)
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, array('label' => 'Generate Character'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chargenerator = $form->getData();
            return $this->redirectToRoute('todo_displaychar', array(
                'name' => $chargenerator-> getName()
                ));
        }

        return $this->render('todo/generate.html.twig', array(
            'form' => $form->createView()));
    }

    /**
     * @Route("/todo/displaychar/{name}", name="todo_displaychar")
     */
    public function displaycharAction($name){
        $stats = array();
        $stats = $this->rollCharacter();

        return $this->render('todo/displaychar.html.twig',
            array(
                'name' => $name,
                'stats' => $stats
        ));
    }

// ---------------------------- FILE METADATA REVIEW ------------------------

    /**
     * @Route("/todo/directory/", name="todo_directory")
     */
    public function directoryAction(Request $request){
        $directory = new Directory();

        $form = $this->createFormBuilder($directory)
            ->add('directory', TextType::class)
            ->add('submit', SubmitType::class, array('label' => 'THIS IS A MEANINGLESS FIELD. TYPE ANYTHING AND CLICK.'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $directory = $form->getData();
            var_dump($directory);
            die('-'.$_SERVER['DOCUMENT_ROOT'].'-');
            return $this->redirectToRoute('todo_file_data', array(
                //'directory' => $directory-> getDirectory()
                //'directory' => __DIR__
                'directory' => $directory
                ));
        }

        return $this->render('todo/directory.html.twig', array(
            'form' => $form->createView()));

    }


    /**
     * @Route("/todo/filedata/directory/", name="todo_file_data")
     */
    public function filedataAction($directory){
        $finder = new Finder();
        //die(__DIR__);
        //added TESTFILES directory to the project root to test putting pdfs into
        $directory = __DIR__.'/../../../TESTFILES/';
        $iterator = $finder->files()->in(__DIR__.'/../../../TESTFILES/');  //replaced __DIR__ and can't figure out how to pass in a directory path

        $i=0;
        $files = array();
        foreach ($iterator as $file) {
            //print $file->getRealpath()."\n";
            $files[$i] = $file->getRealpath();
            $i++;
        } 

        $shellCommandBase = "java -jar ".__DIR__.'../../../Jars/'."tika-app-1.14.jar\" --metadata \"";
        //$shellCommandBase = "java -jar \"C:\\jars\\tika-app-1.14.jar\" --metadata \"";
        $filesMetadata = array();       //initializing array of arrays of key value pairs per file
        $filesKeys = array();           //initializing array of arrays of keys per file
        for ($j=0;$j<count($files);$j++) {
            //$filesMetadata[$j] = TikaWrapper::getMetaData($files[$j]);
            $command = $shellCommandBase.$files[$j].'"';
            exec($command, $output);

            $d = array();
            unset($oneFile);
            for ($k=0;$k<count($output);$k++) {
                unset($d);
                $d = explode(': ', $output[$k]);
                $oneFile[$d[0]] = $d[1];
            }

            $filesMetadata[$j] = $oneFile;
            $filesKeys[$j] = array_keys($oneFile);
        }   

        

        return $this->render('todo/filedata.html.twig',
            array(
                'directory' => $directory,
                'filenames' => $filesMetadata,
                'filekeys' => $filesKeys
        ));
    }

// ------------------------ CHARACTER GENERATION CODING ---------------------

    public function d6() {
        $roll = mt_rand(1,6);
        return ($roll);
    }

    public function rollDice($numberOfDice) {
        $total = 0;     // starter for total of dicerolls
        $least = 7;     // starter for figuring out which die to drop

        // roll dice numberOfDice times and drop the least value from the total
        for ($i=0;$i<$numberOfDice;$i++)
        {   
            $temp = $this->d6();
            if ($temp < $least)
                {$least = $temp;}
            $total = $total + $temp;
        }

        $total = $total - $least;

        return ($total);
    }

    public function rollCharacter() {
        $stats = array();    
        $diceNum = 4;

        // generates array of 6 diceRoll results (4d6 drop lowest)
        for ($i=0;$i<6;$i++)
        {
            $temp = $this->rollDice($diceNum);
            $stats[$i] = $temp;
        }

        return ($stats);
    }  

}
