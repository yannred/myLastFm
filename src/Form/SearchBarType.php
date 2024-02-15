<?php

namespace App\Form;

use App\Data\SearchBarData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
      ->add('to', DateType::class, ['required' => false])

      ->add('trackName', TextType::class, ['required' => false])
      ->add('artistName', TextType::class, ['required' => false])
      ->add('albumName', TextType::class, ['required' => false])

      ->add('groupBy', ChoiceType::class, [
        'choices' => [
          'None' => SearchBarData::GROUP_BY_NONE,
          'Artist' => SearchBarData::GROUP_BY_ARTIST,
          'Album' => SearchBarData::GROUP_BY_ALBUM
        ]])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => SearchBarData::class,
    ]);
  }
}
