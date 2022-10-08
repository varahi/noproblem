<?php

namespace App\Form\User;

use App\Entity\User;
use App\Entity\City;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                    'disabled' => true,
                    'attr' => [
                        'placeholder' => 'Your email',
                        'class' => 'form-control',
                    ],
                    'label' => 'Your email',
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'First name',
                        'class' => 'form-control',
                    ],
                    'label' => 'First name',
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Last name',
                        'class' => 'form-control',
                    ],
                    'label' => 'Last name',
                    'translation_domain' => 'forms',
                ]
            )
            ->add('city', EntityType::class, [
                'class' => City::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'Your city',
                'translation_domain' => 'forms',
                'required' => true,
            ])
            ->add(
                'address',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Your address',
                        'class' => 'form-control',
                    ],
                    'label' => 'Your address',
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'about',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'About',
                        'class' => 'form-control textarea-form-control',
                    ],
                    'label' => 'About',
                    'translation_domain' => 'forms',
                ]
            )

            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'required' => false,
                'type' => PasswordType::class,
                'mapped' => false,
                //'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    //new NotBlank([
                    //    'message' => 'Пожалуйста введите пароль',
                    //]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен состоять как минимм из {{ limit }} символов',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                //'first_options' => ['label' => 'form.password'],
                //'second_options' => ['label' => 'form.confirm_password'],
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Password'
                    ],
                    'label' => 'Password'
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Confirm password'
                    ],
                    'label' => 'Confirm password'
                ],
                'invalid_message' => 'Your password does not match the confirmation',
                'translation_domain' => 'forms',
            ])

            ->add(
                'phone',
                TelType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Your phone',
                        'class' => 'form-control',
                    ],
                    'label' => 'Your phone',
                    'translation_domain' => 'forms',
                ]
            )

            ->add('avatar', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/webp',
                            'image/vnd.wap.wbmp'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])
                ],
                'attr' => [
                    'onchange' => 'readURL(this);'
                ],
                'label' => false,
                'translation_domain' => 'forms',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
