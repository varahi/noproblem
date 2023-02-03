<?php

namespace App\Form\Worksheet;

use App\Entity\Accommodation;
use App\Entity\AdditionalInfo;
use App\Entity\Busyness;
use App\Entity\Education;
use App\Entity\Worksheet;
use App\Entity\Task;
use App\Entity\Experience;
use App\Entity\Citizen;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class WorksheetPsychologistFormType extends AbstractType
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
                TextType::class,
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

//            ->add('isFree', CheckboxType::class, [
//                'mapped' => false,
//                'required' => false,
//                'label' => false,
//                'attr' => [
//                    'class' => 'white',
//                ],
//            ])
            ->add(
                'additionalEducation',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'additionalEducation',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'yearsOfPractice',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'yearsOfPractice',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
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
                    'class' => '_white',
                ],
            ])
            ->add('paymentByMonth', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => '_white',
                ],
            ])

            ->add('isDemo', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'white',
                ],
            ])
            ->add('tasks', EntityType::class, [
                'class' => Task::class,
                'multiple'  => true,
                'expanded'  => true,
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

            /*->add('passportPhoto', FileType::class, [
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
