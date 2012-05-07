<?php

class Project_Create_Form extends MFW_Form
{
    function __construct()
    {
        parent::__construct(True);

        $this->addField('title', new MFW_Form_Field_Text(array('required'=>true)));
        $this->addField('slug', new MFW_Form_Field_Text(array('required'=>true)));
        $this->addField('intro', new MFW_Form_Field_Textarea(array('required'=>true)));
        $this->addField('keywords', new MFW_Form_Field_Text(array('required'=>false)));
        $this->addField('description', new MFW_Form_Field_Textarea(array('required'=>false)));
        $this->addField('image', new MFW_Form_Field_File(array('required'=>false,
                                                               'max_size'=>20971520,
                                                               'save_path'=>MFW_SITE_PATH.'/public/img/projects',
                                                               'save_name'=>'hash',
                                                               'allowed_extensions' => array('png', 'jpg', 'jpeg', 'gif'))));
        $this->addField('github_username', new MFW_Form_Field_Text(array('required'=>false)));
        $this->addField('github_repo', new MFW_Form_Field_Text(array('required'=>false)));
        $this->addField('download', new MFW_Form_Field_Text(array('required'=>false)));
        $this->addField('version', new MFW_Form_Field_Text(array('required'=>false)));
        $this->addField('submit', new MFW_Form_Field_Submit(array('initial'=>'Save new project')));
    }

    public function isValid()
    {
        if ($this->getField('github_username')->getData() || $this->getField('github_repo')->getData()) {
            if (!$this->getField('github_username')->getData()) {
                $this->getField('github_username')->addError('Please provide a username');
            }

            if (!$this->getField('github_repo')->getData()) {
                $this->getField('github_repo')->addError('Please provide a repo name');
            }
        }

        return parent::isValid();
    }
}

class Project_Edit_Form extends Project_Create_Form
{
    function __construct($project)
    {
        parent::__construct();

        $this->addField('id', new MFW_Form_Field_Hidden(array('required'=>true, 'initial'=>$project->id)));
        $this->getField('title')->setInitial($project->title);
        $this->getField('slug')->setInitial($project->slug);
        $this->getField('intro')->setInitial($project->intro);
        $this->getField('keywords')->setInitial($project->keywords);
        $this->getField('description')->setInitial($project->description);
        if (property_exists($project->github, 'username')) {
            $this->getField('github_username')->setInitial($project->github->username);
            $this->getField('github_repo')->setInitial($project->github->repo);
        }
        $this->getField('download')->setInitial($project->download);
        $this->getField('version')->setInitial($project->version);
        $this->getField('submit')->setInitial('Update project');
    }
}

class User_Register_Form extends MFW_Form
{
    function __construct()
    {
        parent::__construct(True);

        $this->addField('username', new MFW_Form_Field_Text(array('required'=>true)));
        $this->addField('password', new MFW_Form_Field_Password(array('required'=>true)));
        $this->addField('email', new MFW_Form_Field_Email(array('required'=>true)));
        $this->addField('submit', new MFW_Form_Field_Submit(array('initial'=>'Register')));
    }
}

class User_Login_Form extends MFW_Form
{
    function __construct()
    {
        parent::__construct(True);

        $this->addField('username', new MFW_Form_Field_Text(array('required'=>true,
                                                                  'attributes'=>array('placeholder'=>'Username'))));
        $this->addField('password', new MFW_Form_Field_Password(array('required'=>true,
                                                                      'attributes'=>array('placeholder'=>'Password'))));
        $this->addField('submit', new MFW_Form_Field_Submit(array('initial'=>'')));
    }
}

class Contact_Form extends MFW_Form
{
    function __construct()
    {
        parent::__construct(True);

        $this->addField('name', new MFW_Form_Field_Text(array('required'=>true)));
        $this->addField('email', new MFW_Form_Field_Email(array('required'=>true)));
        $this->addField('subject', new MFW_Form_Field_Text(array('required'=>true)));
        $this->addField('message', new MFW_Form_Field_Textarea(array('required'=>true,
                                                                     'attributes'=>array('rows'=>15))));
        $this->addField('submit', new MFW_Form_Field_Submit(array('initial'=>'Send')));
    }
}