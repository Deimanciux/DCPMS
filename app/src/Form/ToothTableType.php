<?php

declare(strict_types=1);

namespace App\Form;

use App\Repository\PositionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ToothTableType extends AbstractType
{
    public function __construct(
        private PositionRepository $positionRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $positions = $this->positionRepository->findAll();

        foreach ($positions as $position) {
            $builder->add('tooth' . $position->getPosition(), CheckboxType::class, [
                'label' => $position->getTitle(). '[ ' . $position->getPosition() . ']',
                'required' => false,
                'attr' => [
                    'checked' => true
                ]
            ]);
        }
    }
}
