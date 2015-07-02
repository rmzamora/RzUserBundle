<?php

/*
 * This file is part of the RzUserBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\UserBundle\Admin\Entity;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserAdmin extends BaseUserAdmin
{
    protected $securityContext;

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('email')
            ->end()
                ->with('Groups')
                ->add('groups')
            ->end()
            ->with('Profile')
                ->add('dateOfBirth')
                ->add('firstname')
                ->add('lastname')
                ->add('website')
                ->add('biography')
                ->add('gender')
                ->add('locale')
                ->add('timezone')
                ->add('phone')
            ->end()
            ->with('Social')
                ->add('facebookUid')
                ->add('facebookName')
                ->add('twitterUid')
                ->add('twitterName')
                ->add('gplusUid')
                ->add('gplusName')
            ->end()
            ->with('Security')
                ->add('token')
                ->add('twoStepVerificationCode')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username', null, array('footable'=>array('attr'=>array('data_toggle'=>true))))
            ->add('email', null, array('footable'=>array('attr'=>array('data_hide'=>'phone'))))
            //->add('groups', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('locked', null, array('editable' => true, 'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('createdAt', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('updatedAt', null, array('footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'Show' => array('template' => 'SonataAdminBundle:CRUD:list__action_show.html.twig'),
                        'Edit' => array('template' => 'SonataAdminBundle:CRUD:list__action_edit.html.twig'),
                        'Delete' => array('template' => 'SonataAdminBundle:CRUD:list__action_delete.html.twig')),
                    'footable'=>array('attr'=>array('data_hide'=>'phone,tablet')),
            ))
        ;

//        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
//            $listMapper
//                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig',
//                                                       'footable'=>array('attr'=>array('data_hide'=>'phone,tablet'))))
//            ;
//        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false))
            ->end()
            ->with('Groups')
                ->add('groups', 'sonata_type_model', array('required' => false,
                                                           'multiple' => true,
                                                           'select2'=>true,
                                                           'by_reference' => false,
                                                           'error_bubbling'=>true,
                                                           /*'attr'=>array('class'=>'span10'),*/))
            ->end()
            ->with('Profile')

                ->add('dateOfBirth', 'birthday', array('required' => false))
                ->add('firstname', null, array('required' => false))
                ->add('lastname', null, array('required' => false))
                ->add('website', 'url', array('required' => false))
                ->add('biography', 'text', array('required' => false))
                ->add('gender', 'choice', array(
                                   'choices' => array(
                                       UserInterface::GENDER_UNKNOWN => 'gender_unknown',
                                       UserInterface::GENDER_FEMALE  => 'gender_female',
                                       UserInterface::GENDER_MAN     => 'gender_male',
                                   ),
                                   'required' => true,
                                   'translation_domain' => $this->getTranslationDomain()
                               ))
                ->add('locale', 'locale', array('required' => false, 'select2'=>true))
                ->add('timezone', 'timezone', array('required' => false, 'select2'=>true))
                ->add('phone', null, array('required' => false))
            ->end()
            ->with('Social')
                ->add('facebookUid', null, array('required' => false))
                ->add('facebookName', null, array('required' => false))
                ->add('twitterUid', null, array('required' => false))
                ->add('twitterName', null, array('required' => false))
                ->add('gplusUid', null, array('required' => false))
                ->add('gplusName', null, array('required' => false))
            ->end()
        ;

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Management')
                ->add('roles', 'sonata_security_roles', array(
                                 'multiple' => true,
                                 'multiselect_enabled' => true,
                                 'required' => false,
                             ))
                ->add('locked', 'checkbox', array('required' => false))
                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('credentialsExpired', null, array('required' => false))
                ->end()
            ;
        }

        $formMapper
            ->with('Security')
            ->add('token', null, array('required' => false))
            ->add('twoStepVerificationCode', null, array('required' => false))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('locked')
            ->add('email')
            ->add('groups', null ,array('field_options'=> array('selectpicker_dropup' => true,),
                                        'operator_options'=>array('selectpicker_dropup' => true)))
        ;
    }

    public function setSecurityContext(SecurityContextInterface $securityContext) {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext() {
        return $this->securityContext;
    }
}
