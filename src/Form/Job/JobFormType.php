<?php

namespace App\Form\Job;

use App\Entity\Category;
use App\Entity\Citizen;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Education;
use App\Entity\Experience;
use App\Entity\Job;
use App\Entity\Task;
use App\Entity\AdditionalInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class JobFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Title of the job',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'contactFullName',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Contact Full Name',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Requirements',
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
                        'placeholder' => 'Payment',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'age',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Age',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'messages',
                ]
            )
            ->add('experience', EntityType::class, [
                'class' => Experience::class,
                'multiple'  => false,
                'expanded'  => true,
                //'label' => 'Experience',
                'label' => false,
                'required' => true,
            ])
            ->add('education', EntityType::class, [
                'class' => Education::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => '',
                    'class' => 'select_education nona',
                ],
            ])
            ->add('citizen', EntityType::class, [
                'class' => Citizen::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
            ])
            ->add('startNow', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false
            ])
            ->add('tasks', EntityType::class, [
                'class' => Task::class,
                'multiple'  => true,
                'expanded'  => true,
                //'label' => 'Experience',
                'label' => false,
                'required' => true,
            ])
            ->add(
                'anotherTask',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Another Task',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'anotherCitizen',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Another Citizen',
                        'class' => 'text_input',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add('startDate', DateType::class, [
                'label'     => false,
                'required' => false,
                'widget' => 'single_text',
                //'format' => 'MM/DD/yyyy',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
                //'input'  => 'datetime_immutable',
                'attr' => [
                    'class' => 'text_input date',
                    //'placeholder' => 'Start Date',
                ]
            ])
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
            ->add('city', EntityType::class, [
                'class' => City::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'text_input',
                ],
            ])
            ->add('district', EntityType::class, [
                'class' => District::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'text_input',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => false,
                'required' => true,
            ])
            ->add('additional', EntityType::class, [
                'class' => AdditionalInfo::class,
                'multiple'  => true,
                'expanded'  => true,
                //'label' => 'Experience',
                'label' => false,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}


/*->add(
    'experience',
    ChoiceType::class,
    [
        'required' => true,
        'label' => 'Experience',
        'translation_domain' => 'messages',
        'choices'  => [
            'Нет' => null,
            'Менее 6 месяцев' => '1',
            '6-12 месяцев' => '2',
            '2-5 лет' => '3',
            'более 5 лет' => '4',
        ],
        'data' => 'null'
    ]
)*/
