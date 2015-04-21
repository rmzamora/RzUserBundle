<?php

/*
 * This file is part of the RzUserBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword as OldUserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileType extends AbstractType
{
    protected $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (class_exists('Symfony\Component\Security\Core\Validator\Constraints\UserPassword')) {
            $constraint = new UserPassword();
        } else {
            // Symfony 2.1 support with the old constraint class
            $constraint = new OldUserPassword();
        }

        $this->buildUserForm($builder, $options);

        $builder->add('current_password', 'password', array(
                                            'label' => 'form.current_password',
                                            'translation_domain' => 'FOSUserBundle',
                                            'mapped' => false,
                                            'constraints' => $constraint,
                                        ));

    }

    /**
     * {@inheritdoc}
     *
     * @todo Remove it when bumping requirements to SF 2.7+
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rz_user_profile';
    }


    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'RzUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'RzUserBundle', 'read_only'=>true, 'attr'=>array('class'=>'span12')))
            ->add('firstname', null, array('label' => 'form.firstname', 'translation_domain' => 'RzUserBundle', 'attr'=>array('class'=>'span12')))
            ->add('lastname', null, array('label' => 'form.lastname','translation_domain' => 'RzUserBundle', 'attr'=>array('class'=>'span12')))
            ->add('website', null, array('label' => 'form.website', 'translation_domain' => 'RzUserBundle', 'attr'=>array('class'=>'span12')))
            ->add('phone', null, array('required' => false, 'label' => 'form.phone', 'translation_domain' => 'RzUserBundle', 'attr'=>array('class'=>'span12')))
            ->add('dateOfBirth', 'birthday', array('label' => 'form.dateOfBirth','translation_domain' => 'RzUserBundle', 'attr'=>array('class'=>'span12')))
            ->add('gender', 'choice', array(
                              'label' => 'form.gender',
                              'choices' => array(
                                  UserInterface::GENDER_UNKNOWN => 'gender_unknown',
                                  UserInterface::GENDER_FEMALE  => 'gender_female',
                                  UserInterface::GENDER_MAN     => 'gender_male',
                              ),
                              'required' => true,
                              'translation_domain' => 'RzUserBundle',
                              'attr'=>array('class'=>'span12')
                          ));
    }
}
