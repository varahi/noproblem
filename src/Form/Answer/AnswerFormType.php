<?php

namespace App\Form\Answer;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'answer',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Your message',
                        'class' => 'form-control textarea-form-control',
                    ],
                    'label' => false,
                    'translation_domain' => 'forms',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
