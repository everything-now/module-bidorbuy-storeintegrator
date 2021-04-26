<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Catalog\Model\Category;

class ProductConditionCategories extends Field
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    protected $categoryHelper;
    // phpcs:enable

    public function __construct(
        Context $context,
        BidorbuyHelper $bidorbuyHelper,
        Category $categoryModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->bidorbuyHelper = $bidorbuyHelper;
        $this->categoryModel = $categoryModel;
    }

    /**
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found
     * phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $secondhandCategories = $this->bidorbuyHelper->getCore()->getSettings()
            ->getSecondhandProductConditionCategories();
        $refurbishedCategories = $this->bidorbuyHelper->getCore()->getSettings()
            ->getRefurbishedProductConditionCategories();

        $secondhandCategoriesName = bobsi\Settings::NAME_PRODUCT_CONDITION_SECONDHAND_CATEGORIES;
        $refurbishedCategoriesName = bobsi\Settings::NAME_PRODUCT_CONDITION_REFURBISHED_CATEGORIES;

        $secondhandCategoriesHtml = "<select id='$secondhandCategoriesName' 
                                 class='bobsi-categories-select' 
                                 name='{$secondhandCategoriesName}[]' 
                                 multiple='multiple' 
                                 size='9'>";

        $newCategoriesHtml = "<select id='productConditionNewCategories' 
                                 class='bobsi-categories-select' 
                                 name='productConditionNewCategories[]' 
                                 multiple='multiple' 
                                 size='9'>";

        $refurbishedCategoriesHtml = "<select id='$refurbishedCategoriesName' 
                                 class='bobsi-categories-select' 
                                 name='{$refurbishedCategoriesName}[]' 
                                 multiple='multiple' 
                                 size='9'>";

        $categoriesCollection = $this->categoryModel->getTreeModel()
            ->load()
            ->getCollection()
            ->addFieldToFilter('is_active', 1);

        $categoriesIterator = $categoriesCollection->getIterator();
        $categoriesIteratorData = $categoriesIterator->getArrayCopy();

        $categories = [];
        $categories[0] = 'Uncategorized';
        foreach ($categoriesIteratorData as $category) {
            $categories[$category->getId()] = $category->getName();
        }

        foreach ($categories as $categoryId => $categoryName) {
            $option = '<option  value="' . $categoryId . '">' . $categoryName . '</option>';
            if (in_array($categoryId, $secondhandCategories)) {
                $secondhandCategoriesHtml .= $option;
                continue;
            } elseif (in_array($categoryId, $refurbishedCategories)) {
                $refurbishedCategoriesHtml .= $option;
                continue;
            }

            $newCategoriesHtml .= $option;
        }

        $secondhandCategoriesHtml .= '</select>';
        $newCategoriesHtml .= '</select>';
        $refurbishedCategoriesHtml .= '</select>';

        $html[] = '<table>
                    <tbody>
                    <tr>
                    <td style="height: 25px"><strong>Product Condition</strong></td>
                    </tr>
                        <tr>
                            <td>
                                <label for="ProductConditionSecondhandCategories">Secondhand</label>
                            </td>
                            <td></td>
                            <td>
                                <label for="ProductConditionNewCategories">New</label>
                            </td>
                            <td></td>
                            <td>
                                <label for="ProductConditionRefurbishedCategories">Refurbished</label>
                            </td>
                        </tr>';
        $html[] = '<tr><td>' . $secondhandCategoriesHtml . '</td>';
        $html[] = '<td class="cats-middle">
                    <p class="submit">
                        <button name="includeProductConditionSecondhandCategories" 
                        id="includeProductConditionSecondhandCategories" class="button" type="button">
                        <&nbsp;Include
                        </button>
                    </p>
                    <p class="submit">
                        <button name="excludeProductConditionSecondhandCategories" 
                        id="excludeProductConditionSecondhandCategories" class="button" type="button">
                        >&nbsp;Exclude
                        </button>
                    </p>               
                   </td>';
        $html[] = '<td>' . $newCategoriesHtml . '</td>';
        $html[] = '<td class="cats-middle">
                    <p class="submit">
                        <button name="includeProductConditionRefurbishedCategories" 
                        id="includeProductConditionRefurbishedCategories" class="button" type="button">
                        >&nbsp;Include
                        </button>
                    </p>
                    <p class="submit">
                        <button name="excludeProductConditionRefurbishedCategories" 
                        id="excludeProductConditionRefurbishedCategories" class="button" type="button">
                            <span><&nbsp;Exclude</span>
                        </button>
                    </p>
                   </td>';
        $html[] = '<td>' . $refurbishedCategoriesHtml . '</td></tr></tbody></table>';
        $html[] = "<input type='hidden' 
                          name='groups[productSettings][fields][$secondhandCategoriesName][value]'
                          id='bidorbuystoreintegrator_productSettings_productConditionSecondhandCategories'
                          value='" . implode(',', $secondhandCategories) . "'/>";
        $html[] = "<input type='hidden' 
                          name='groups[productSettings][fields][$refurbishedCategoriesName][value]'
                          id='bidorbuystoreintegrator_productSettings_productConditionRefurbishedCategories'
                          value='" . implode(',', $refurbishedCategories) . "'/>";

        return implode($html);
    }
}
