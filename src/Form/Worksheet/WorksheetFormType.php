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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorksheetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('name')
            ->add('payment')
            ->add('startNow')
            ->add('startDate')
            ->add('age')
            ->add('description')
            ->add('additional')
            ->add('schedule')
            ->add('preferredContactMethod')
            ->add('created')
            ->add('hidden')
            ->add('city')
            ->add('district')
            ->add('education')
            ->add('experience')
            ->add('citizen')
            ->add('user')
            ->add('category')*/

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
            ->add('experience', EntityType::class, [
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
            ])
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
            'data_class' => Worksheet::class,
        ]);
    }
}
