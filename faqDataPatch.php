<?php
/**
 * @category Summit
 * @package Summit\MarketRollout
 * @copyright Copyright (c) 2022 Summit Media Limited (http://www.summit.co.uk/)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Summit\MarketRollout\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magestore\Faq\Model\FaqvalueFactory as FaqValueFactory;
use Magestore\Faq\Model\ResourceModel\Faq as FaqResource;
use Magestore\Faq\Model\ResourceModel\Faqvalue\CollectionFactory as FaqValueCollectionFactory;
use Magestore\Faq\Model\ResourceModel\Faqvalue as FaqValueResource;

class ChangeFaqTitlesAndStatuses implements DataPatchInterface
{
    //сделать ввиде массива
    const TITLE1 = 'What happens if I do not renew Remote/Protect?';
    const TITLE2 = 'What happens if I do not renew Remote/Protect';
    const TITLE3 = 'What are the benefits of Secure Tracker?';
    const TITLE4 = 'What happens if I do not renew?';
    const TITLE5 = '데이터 요금제가 포함된 Wi-Fi는 무엇입니까?';
    const TITLE6 = 'Secure Tracker의 이점은 무엇입니까?';
    const TITLE7 = 'セキュア トラッカーの利点とは何ですか？';
    const TITLE8 = '데이터 요금제가 적용된 Wi-Fi는 무엇입니까?';
    const DESCRIPTION1 = '<p>U kunt een restitutieverzoek indienen binnen 14 dagen na ingang van uw abonnement. Neem contact op met ons Customer Relationship Centre op {{customVar code=crc_phone}} of stuur een e-mail met uw verzoek naar {{customVar code=refund_email}}. Als uw aanvraag wordt goedgekeurd, verwerken wij uw restitutie via de oorspronkelijke betalingsmethode en krijgt u een melding per e-mail nadat deze is verwerkt. Het kan 5-10 werkdagen duren voordat het geld op uw bankrekening staat.</p>
<p>[Disclaimer servicedeactivering] Als u een of meer van uw InControl-abonnementen niet verlengt, zullen de bijbehorende diensten worden uitgeschakeld op uw auto op de dag van de vervaldatum. U kunt nog steeds profiteren van deze diensten tot de dag waarop uw abonnement afloopt.</p>';
    const DESCRIPTION2 = '<p>U kunt een restitutieverzoek indienen binnen 14 dagen na ingang van uw abonnement. Neem contact op met ons Customer Relationship Centre op {{customVar code=crc_phone}} of stuur een e-mail met uw verzoek naar {{customVar code=refund_email}}. Als uw aanvraag wordt goedgekeurd, verwerken wij uw restitutie via de oorspronkelijke betalingsmethode en krijgt u een melding per e-mail nadat deze is verwerkt. Het kan 5-10 werkdagen duren voordat het geld op uw bankrekening staat.</p>
';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var FaqValueCollectionFactory
     */
    protected $faqValueCollectionFactory;

    /**
     * @var FaqResource
     */
    protected $faqResource;

    /**
     * @var FaqValueFactory
     */
    protected $faqValueFactory;

    /**
     * @var FaqValueResource
     */
    protected $faqValueResource;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param FaqValueCollectionFactory $faqValueCollectionFactory
     * @param FaqResource $faqResource
     * @param FaqValueFactory $faqValueFactory
     * @param FaqValueResource $faqValueResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        FaqValueCollectionFactory $faqValueCollectionFactory,
        FaqResource $faqResource,
        FaqValueFactory $faqValueFactory,
        FaqValueResource $faqValueResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->faqValueCollectionFactory = $faqValueCollectionFactory;
        $this->faqResource = $faqResource;
        $this->faqValueFactory = $faqValueFactory;
        $this->faqValueResource = $faqValueResource;
    }

    /**
     * @param $faqIdData
     * @return void
     * @method int setData()
     * @throws AlreadyExistsException
     */
    protected function switchOffFaqByTitleId ($faqIdData)
    {
        foreach ($faqIdData as $id) {
            $id += 2;
            $faqData = $this->faqValueFactory->create();
            $this->faqValueResource->load($faqData, $id, 'faq_value_id');
            $value = $faqData->getData('value');
            if ($value == 1) {
                $faqData->setData('value', '2');
            }
            $this->faqValueResource->save($faqData);
        }
    }

    protected function getFaqIdData ($field1, $condition1, $field2 = NULL, $condition2 = NULL, $field3 = NULL, $condition3 = NULL)
    {
        if (is_null($field2) AND is_null($field3)) {
            $faqIdData = $this->faqValueCollectionFactory
                ->create()
                ->addFieldToFilter($field1, $condition1)
                ->getAllIds();
            return $faqIdData;
        } elseif (is_null($field3)) {
            $faqIdData = $this->faqValueCollectionFactory
                ->create()
                ->addFieldToFilter($field1, $condition1)
                ->addFieldToFilter($field2, $condition2)
                ->getAllIds();
            return $faqIdData;
        } else {
            $faqIdData = $this->faqValueCollectionFactory
                ->create()
                ->addFieldToFilter($field1, $condition1)
                ->addFieldToFilter($field2, $condition2)
                ->addFieldToFilter($field3, $condition3)
                ->getAllIds();
            return $faqIdData;
        }
    }

    /**
     * @param $faqId
     * @param $fieldForChange
     * @param $value
     * @return void
     * @throws AlreadyExistsException
     */
    protected function updateFaqValue ($faqId, $fieldForChange, $value)
    {
        $faqData = $this->faqValueFactory->create();
        $this->faqValueResource->load($faqData, $faqId, 'faq_value_id');
        $faqData->setData($fieldForChange, $value);
        $this->faqValueResource->save($faqData);
    }

    /**
     * @return void
     * @throws AlreadyExistsException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $faqIdData = $this->getFaqIdData('value', self::TITLE2);
        foreach ($faqIdData as $id) {
            $this->updateFaqValue($id, 'value', self::TITLE1);
        }

        $faqIdData = $this->getFaqIdData('value', self::TITLE1);
        foreach ($faqIdData as $id) {
            $faqData = $this->faqValueFactory->create();
            $this->faqValueResource->load($faqData, $id, 'faq_value_id');
            $storeIds[] = $faqData->getData('store_id');
        }

        foreach (array_count_values($storeIds) as $storeId => $amount) {
            if ($amount > 1) {
                $moreThanOneStoreId[] = $storeId;
            }
        }

        if(isset($moreThanOneStoreId)){
            foreach ($moreThanOneStoreId as $storeId) {
                $faqIdData = $this->getFaqIdData('value', self::TITLE1, 'store_id', $storeId);
                array_pop($faqIdData);
                $this->switchOffFaqByTitleId($faqIdData);
            }
        }
        //SELECT * FROM faq_value WHERE value LIKE 'What happens if I do not renew Remote/Protect%';

        $faqIdData = $this->getFaqIdData('value', self::TITLE3);
        $this->switchOffFaqByTitleId($faqIdData);
        //SELECT * FROM faq_value WHERE faq_id = 48 AND store_id = 44;

        $faqIdData = $this->getFaqIdData('value', self::TITLE4);
        $this->switchOffFaqByTitleId($faqIdData);
        //SELECT * FROM faq_value WHERE faq_id = 77 AND store_id = 43;

        $faqIdData = $this->getFaqIdData('value', self::DESCRIPTION2);
        foreach ($faqIdData as $id) {
            $this->updateFaqValue($id, 'value', self::DESCRIPTION1);
        }
        //!! SELECT * FROM faq_value WHERE value LIKE '<p>U kunt een restitutieverzoek indienen binnen 14 dagen na ingang van uw abonnement. Neem contact op met ons Customer Relationship Centre op {{customVar code=crc_phone}} of stuur een e-mail met uw verzoek naar {{customVar code=refund_email}}. Als uw aanvraag wordt goedgekeurd, verwerken wij uw restitutie via de oorspronkelijke betalingsmethode en krijgt u een melding per e-mail nadat deze is verwerkt. Het kan 5-10 werkdagen duren voordat het geld op uw bankrekening staat.</p>%';

        $faqIdData = $this->getFaqIdData('value', self::TITLE8);
        foreach ($faqIdData as $id) {
            $this->updateFaqValue($id, 'value', self::TITLE5);
        }
        //SELECT * FROM faq_value WHERE faq_id = 74 AND store_id = 50;

        $faqIdData = $this->getFaqIdData('value', self::TITLE6);
        $this->switchOffFaqByTitleId($faqIdData);
        //SELECT * FROM faq_value WHERE faq_id = 48 AND store_id = 49;

        $faqIdData = $this->getFaqIdData('value', self::TITLE7);
        $this->switchOffFaqByTitleId($faqIdData);
        //SELECT * FROM faq_value WHERE faq_id = 48 AND store_id = 48;

        $this->moduleDataSetup->getConnection()->endSetup();

    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [
//            \Summit\MarketRollout\Setup\UpgradeMigration\FixEUFaq\UpgradeMigration::class
        ];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }
}
