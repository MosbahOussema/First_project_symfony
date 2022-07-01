<?php
namespace App\Service;

 use App\Repository\ProductRepository;

class Product
{

    const CATEGORIES= ['Mobile', 'Computer','Tablet','Games & Entertainment'];
    /**
     * @var ProductRepository
     */
    private $productRepository;
   /**
     * Product constructor
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getCountByCategory()
    {
        $categories= [];
        foreach(self::CATEGORIES as $c){
            $categories[$c] = $this->productRepository->getCountByCategory($c)[1];// elle va donner un tab de [mobile,5...computer,8]
            
        }             
        return $categories;                                                                                      
    }

}