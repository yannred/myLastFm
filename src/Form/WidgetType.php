<?php

namespace App\Form;

use App\Entity\Widget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WidgetType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('wording', null, ['label' => 'Title', 'required' => true])
      ->add('comment', null, ['label' => 'Comments', 'required' => false])
      ->add('typeWidget', ChoiceType::class, [
        'choices'  => [ '' => 0, ...Widget::TYPES],
        'label' => 'Choice the statistic type',
        'required' => true,
        'choice_value' => static function (?int $i) {
          return null === $i ? 0 : $i;
        }
      ])
      ->add('subTypeWidget', ChoiceType::class, [
        'choices'  => [ '' => 0, ...Widget::SUB_TYPES],
        'label' => 'Choice the chart render type',
        'required' => true,
        'choice_value' => static function (?int $i) {
          return null === $i ? 0 : $i;
        }
      ])
      ->add('fontColor', ColorType::class, [
        'label' => 'Font Color',
        'data' => Widget::WIDGET_DEFAULT_FONT_COLOR,
        'required' => true
      ])
      ->add('backgroundColor', ColorType::class, [
        'label' => 'Background color',
        'data' => Widget::WIDGET_DEFAULT_BACKGROUND_COLOR,
        'required' => true
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Widget::class,
    ]);
  }
}
