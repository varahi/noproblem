<?php

namespace App\Form\Worksheet;

use App\Entity\Accommodation;
use App\Entity\AdditionalInfo;
use App\Entity\Busyness;
use App\Entity\City;
use App\Entity\Education;
use App\Entity\Worksheet;
use App\Entity\Task;
use App\Entity\Experience;
use App\Entity\Citizen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class WorksheetVolounteerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categoryId = $options['categoryId'];
        $builder
            ->add(
                'name',
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
                'age',
                IntegerType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => '',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'messages',
                ]
            )
            ->add('startNow', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'white',
                ],
            ])

//            ->add('isFree', CheckboxType::class, [
//                'mapped' => false,
//                'required' => false,
//                'label' => false,
//                'attr' => [
//                    'class' => 'white',
//                ],
//            ])
            ->add('experience', EntityType::class, [
                'class' => Experience::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
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
            ->add('paymentByHour', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'chkclass',
                ],
            ])
            ->add('paymentByMonth', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'chkclass',
                ],
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
            ->add('tasks', EntityType::class, [
                'class' => Task::class,
                'multiple'  => true,
                'expanded'  => false,
                'by_reference' => false,
                'query_builder' => function (EntityRepository $er) use ($categoryId) {
                    return $er->createQueryBuilder('t')
                        ->join('t.category', 'c')
                        ->where('c.id = :id')
                        ->setParameter(':id', $categoryId)
                    ;
                },
                'label' => 'Employer assigned',
            ])
            ->add('citizen', EntityType::class, [
                'class' => Citizen::class,
                'multiple'  => true,
                'expanded'  => false,
                'label' => 'Citizen',
                'required' => true,
            ])
            ->add('accommodations', EntityType::class, [
                'class' => Accommodation::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'Accommodations',
                'required' => true,
            ])
//            ->add('busynesses', EntityType::class, [
//                'class' => Busyness::class,
//                'multiple'  => true,
//                'expanded'  => true,
//                'label' => 'busynesses',
//                'required' => true,
//            ])
            ->add(
                'customBusynesses',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'customBusynesses',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add('additional', EntityType::class, [
                'class' => AdditionalInfo::class,
                'multiple'  => true,
                'expanded'  => false,
                'label' => 'busynesses',
                'required' => false,
            ])

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
                    //'class' => 'field field__file',
                ],
                'label' => false,
                'translation_domain' => 'forms',
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'City',
                'required' => false,
                'placeholder' => 'Выберите город',
                'empty_data' => null,
            ])
            ->add(
                'clientAge',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => false,
                    'translation_domain' => 'messages',
                    'choices'  => [
                        'Нет' => null,
                        '0-1 года' => '0-1 года',
                        '2-3 года' => '2-3 года',
                        '4-6 лет' => '4-6 лет',
                        '7-10 лет' => '7-10 лет',
                        '11-14 лет' => '11-14 лет',
                    ],
                    'data' => 'null'
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

/*            ->add(
                'passportSeries',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'passportSeries',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'passportNumber',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'passportNumber',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add('passportDateOfIssue', DateType::class, [
                'label'     => false,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
                'attr' => [
                    'class' => 'text_input date',
                ]
            ])
            ->add(
                'passportPlaceOfIssue',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'passportPlaceOfIssue',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'passportIssuingAuthority',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'passportIssuingAuthority',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )*/

/*            ->add('passportPhoto', FileType::class, [
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
                    //'class' => 'field field__file',
                ],
                'label' => false,
                'translation_domain' => 'forms',
            ])*/

/*            ->add(
                'amountOfChildren',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => false,
                    'translation_domain' => 'messages',
                    'choices'  => [
                        'Нет' => null,
                        '1 ребенок' => '1 ребенок',
                        '2 ребенка' => '2 ребенка',
                        '3 ребенка' => '3 ребенка',
                        '4 ребенка' => '4 ребенка',
                        '5 детей' => '5 детей',
                    ],
                    'data' => 'null'
                ]
            )*/

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Worksheet::class,
        ]);
        $resolver->setRequired([
            'categoryId',
        ]);
    }
}
