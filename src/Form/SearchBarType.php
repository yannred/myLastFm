<?php

namespace App\Form;

use App\Data\SearchBarData;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBarType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('type', HiddenType::class, ['data' => 'query'])
      ->add('from', DateType::class, ['required' => false])
      ->add('to', DateType::class, ['required' => false]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => SearchBarData::class,
    ]);
  }
}
