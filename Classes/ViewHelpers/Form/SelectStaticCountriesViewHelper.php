<?php
namespace Evoweb\SfRegister\ViewHelpers\Form;

/***************************************************************
 * Copyright notice
 *
 * (c) 2011-17 Sebastian Fischer <typo3@evoweb.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Viewhelper to render a selectbox with values of static info tables countries
 *
 * <code title="Usage">
 *  {namespace register=Evoweb\SfRegister\ViewHelpers}
 *  <register:form.SelectStaticCountries name="country" optionLabelField="cnShortDe"/>
 * </code>
 * <code title="Optional label field">
 *  {namespace register=Evoweb\SfRegister\ViewHelpers}
 *  <register:form.SelectStaticCountries name="country" optionLabelField="cnShortDe"/>
 * </code>
 */
class SelectStaticCountriesViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    /**
     * Repository that provides the country models
     *
     * @var \Evoweb\SfRegister\Domain\Repository\StaticCountryRepository
     * @inject
     */
    protected $countryRepository;


    /**
     * Initialize arguments. Cant be moved to parent because
     * of "private $argumentDefinitions = [];"
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('multiple', 'string', 'if set, multiple select field');
        $this->registerTagAttribute('size', 'string', 'Size of input field');
        $this->registerTagAttribute(
            'disabled',
            'string',
            'Specifies that the input element should be disabled when the page loads'
        );
        $this->registerArgument('name', 'string', 'Name of input tag');
        $this->registerArgument('value', 'mixed', 'Value of input tag');
        $this->registerArgument('sortByOptionLabel', 'boolean', 'If true, List will be sorted by label.', false, true);
        $this->registerArgument(
            'allowedCountries',
            'array',
            'Array with countries allowed to be displayed.',
            false,
            []
        );
        $this->registerArgument(
            'property',
            'string',
            'Name of Object Property. If used in conjunction with <f:form object="...">,
             "name" and "value" properties will be ignored.'
        );
        $this->registerArgument(
            'optionValueField',
            'string',
            'If specified, will call the appropriate getter on each object to determine the value.',
            false,
            'cnIso2'
        );
        $this->registerArgument(
            'optionLabelField',
            'string',
            'If specified, will call the appropriate getter on each object to determine the label.',
            false,
            'cnOfficialNameEn'
        );
        $this->registerArgument(
            'selectAllByDefault',
            'boolean',
            'If specified options are selected if none was set before.',
            false,
            false
        );
        $this->registerArgument(
            'errorClass',
            'string',
            'CSS class to set if there are errors for this view helper',
            false,
            'f3-form-error'
        );
    }

    /**
     * Override the initialize method to load all
     * available countries before rendering
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('static_info_tables')) {
            if ($this->hasArgument('allowedCountries') && count($this->arguments['allowedCountries'])) {
                $options = $this->countryRepository->findByCnIso2($this->arguments['allowedCountries']);
            } else {
                $options = $this->countryRepository->findAll();
            }
            $options = $options->toArray();

            if ($this->hasArgument('disabled')) {
                $value = $this->getSelectedValue();
                $value = is_array($value) ? $value : [$value];

                $options = array_filter($options, function ($option) use ($value) {
                    /** @var \Evoweb\SfRegister\Domain\Model\StaticCountry $option */
                    return in_array($option->getCnIso2(), $value);
                });
            }

            $this->arguments['options'] = $options;
        }
    }
}
