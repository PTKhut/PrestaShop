<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Currency;

class ReferencesType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $productConditionChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param Currency $defaultCurrency
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productConditionChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->productConditionChoiceProvider = $productConditionChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('condition', ChoiceType::class, [
                'choices' => $this->productConditionChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => true,
                'label' => $this->trans('Condition', 'Admin.Catalog.Feature'),
                'label_attr' => [
                    'popover' => $this->trans('Not all shops sell new products. This option enables you to indicate the condition of the product. It can be required on some marketplaces.', 'Admin.Catalog.Help'),
                ],
            ])
            ->add('show_condition', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Display condition on product page', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('mpn', TextType::class, [
                'required' => false,
                'label' => $this->trans('MPN', 'Admin.Catalog.Feature'),
                'label_attr' => [
                    'popover' => $this->trans('MPN is used internationally to identify the Manufacturer Part Number.', 'Admin.Catalog.Help'),
                ],
                'constraints' => [
                    new Length(['max' => ProductSettings::MAX_MPN_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('upc', TextType::class, [
                'required' => false,
                'label' => $this->trans('UPC barcode', 'Admin.Catalog.Feature'),
                'label_attr' => [
                    'popover' => $this->trans('This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.', 'Admin.Catalog.Help'),
                ],
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_UPC),
                ],
                'empty_data' => '',
            ])
            ->add('ean_13', TextType::class, [
                'required' => false,
                'error_bubbling' => true,
                'label' => $this->trans('EAN-13 or JAN barcode', 'Admin.Catalog.Feature'),
                'label_attr' => [
                    'popover' => $this->trans('This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.', 'Admin.Catalog.Help'),
                ],
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_EAN_13),
                ],
                'empty_data' => '',
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
                'label' => $this->trans('ISBN', 'Admin.Catalog.Feature'),
                'label_attr' => [
                    'popover' => $this->trans('The International Standard Book Number (ISBN) is used to identify books and other publications.', 'Admin.Catalog.Help'),
                ],
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_ISBN),
                ],
                'empty_data' => '',
            ])
            ->add('reference', TextType::class, [
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'label_attr' => [
                    'popover' => $this->trans('Your reference code for this product. Allowed special characters: .-_#.', 'Admin.Catalog.Help'),
                ],
                'empty_data' => '',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => $this->trans('Condition & References', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h2',
            'required' => false,
            'columns_number' => 3,
        ]);
    }
}
