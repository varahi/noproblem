<?php

namespace App\Form\Ticket;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Ticket title',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Ticket description',
                        'class' => 'form-control textarea-form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
            ->add(
                'priority',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => false,
                    'translation_domain' => 'messages',
                    'choices'  => [
                        'Выберите срочность обращения *' => null,
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ],
                ]
            )
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'autocomplete' => 'email',
                    'placeholder' => 'Your email',
                ],
                'label' => false,
                'translation_domain' => 'forms',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
