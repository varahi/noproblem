<?php

namespace App\Form\Worksheet;

use App\Entity\Category;
use App\Entity\Citizen;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Education;
use App\Entity\Experience;
use App\Entity\Worksheet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class WorksheetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => '',
                        'class' => 'form-control',
                    ],
                    //'label' => 'Title of the job',
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'contactFullName',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Contact Full Name',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Address',
                        'class' => 'text_input',
                    ],
                    'label' => false
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Расскажите о себе, это поможет найти клиентов',
                        'class' => 'text_input',
                    ],
                    //'label' => 'Requirements',
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'reviews',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Вставьте ссылку на отзывы о вашей работе',
                        'class' => 'text_input',
                    ],
                    //'label' => 'Requirements',
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            /*->add('startDate', DateType::class, [
                'label'     => false,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
                'attr' => [
                    'class' => 'text_input date',
                ]
            ])*/
            ->add('startNow', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'white',
                ],
            ])
            ->add('paymentByAgreement', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'white',
                ],
            ])
            ->add(
                'payment',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => '',
                        'class' => 'form-control',
                    ],
                    //'label' => 'Payment',
                    'label' => false,
                    'translation_domain' => 'messages',
                ]
            )
            /*->add(
                'age',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => '',
                        'class' => 'form-control',
                    ],
                    'label' => 'Age',
                    'translation_domain' => 'messages',
                ]
            )*/
            /*->add('experience', EntityType::class, [
                'class' => Experience::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'Experience',
                'required' => true,
            ])
            ->add('education', EntityType::class, [
                'class' => Education::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'Education',
                'required' => true,
            ])
            ->add('citizen', EntityType::class, [
                'class' => Citizen::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'Citizen',
                'required' => true,
            ])*/
            ->add('startNow', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Start Now',
                /*'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],*/
            ])
            /*->add('startDate', DateType::class, [
                'label'     => 'Start Date',
                'required' => false,
                'widget' => 'single_text',
                //'format' => 'MM/DD/yyyy',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
                //'input'  => 'datetime_immutable',
                'attr' => [
                    'class' => 'date'
                ]
            ])*/

            /*->add('city', EntityType::class, [
                'class' => City::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'City',
                'required' => true,
            ])
            ->add('district', EntityType::class, [
                'class' => District::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'District',
                'required' => true,
            ])*/
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
                'placeholder' => 'Выберите категорию'
            ])
            ->add(
                'anotherTask',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Другое',
                        'class' => 'other_help',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add('image', FileType::class, [
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
                    //'onchange' => 'readURL(this);'
                    'class' => 'field field__file',
                ],
                'label' => false,
                'translation_domain' => 'forms',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Worksheet::class,
        ]);
    }
}
