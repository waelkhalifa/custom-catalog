<?php


namespace Wael\CustomCatalog\Controller\Adminhtml\Product;

/**
 * Class Index
 * @package Wael\CustomCatalog\Controller\Adminhtml\Product
 */
class Index extends  \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param  \Magento\Framework\App\Action\Context  $context
     * @param  \Magento\Framework\View\Result\PageFactory  $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
