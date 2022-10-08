<?php

namespace App\Form\Job;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Job;
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
                        'placeholder' => '',
                        'class' => 'form-control',
                    ],
                    'label' => 'Title of the job',
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => '',
                        'class' => 'form-control',
                    ],
                    'label' => 'Requirements',
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
                    'label' => 'Payment',
                    'translation_domain' => 'messages',
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
                    'label' => 'Age',
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
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
            )
            ->add(
                'education',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'Education',
                    'translation_domain' => 'messages',
                    'choices'  => [
                        'Нет' => null,
                        'Начальное' => '1',
                        'Среднее' => '2',
                        'Техническое' => '3',
                        'Неполное высшее' => '4',
                        'Высшее' => '5',
                    ],
                    'data' => 'null'
                ]
            )
            ->add(
                'citizen',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'Citizen',
                    'translation_domain' => 'messages',
                    'choices'  => [
                        'Нет' => null,
                        'РФ' => '1',
                        'Белоруссия' => '2',
                        'Армения' => '3',
                        'Азербайджан' => '4',
                        'Грузия' => '5',
                        'Казахстан' => '6',
                        'Киргизия' => '7',
                        'Молодова' => '8',
                        'Таджикистан' => '9',
                        'Узбекистан' => '10',
                        'Страны ЕС' => '11',
                    ],
                    'data' => 'null'
                ]
            )
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
            ->add('startDate', DateType::class, [
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
            ])
            ->add(
                'address',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => '',
                        'class' => 'form-control textarea-form-control',
                    ],
                    'label' => 'Address',
                ]
            )
            ->add('city', EntityType::class, [
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
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple'  => false,
                'expanded'  => false,
                'label' => 'Category',
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
