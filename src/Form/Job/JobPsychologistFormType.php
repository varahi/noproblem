<?php

namespace App\Form\Job;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobPsychologistFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('created')
            ->add('name')
            ->add('description')
            ->add('age')
            ->add('startDate')
            ->add('address')
            ->add('schedule')
            ->add('hidden')
            ->add('image')
            ->add('startNow')
            ->add('payment')
            ->add('contactFullName')
            ->add('anotherTask')
            ->add('anotherCitizen')
            ->add('latitude')
            ->add('longitude')
            ->add('city')
            ->add('category')
            ->add('client')
            ->add('owner')
            ->add('district')
            ->add('experience')
            ->add('education')
            ->add('citizen')
            ->add('tasks')
            ->add('additional')
            ->add('featuredUsers')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
