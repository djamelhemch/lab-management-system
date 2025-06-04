<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchFilter extends Component  
{  
    public $searchPlaceholder;  
    public $searchValue;  
    public $categories;  
    public $categoryValue;  
    public $categoryName;  
    public $categoryLabel;  
    public $formId;  
    public $tableRoute;
    public $containerId; // Add this property
  
    public function __construct(  
        $searchPlaceholder = 'Search...',  
        $searchValue = '',  
        $categories = [],  
        $categoryValue = '',  
        $categoryName = 'category_id',  
        $categoryLabel = 'Category',  
        $formId = 'search-form',  
        $tableRoute = '',
        $containerId = 'table-container' // Add this parameter
    ) {  
        $this->searchPlaceholder = $searchPlaceholder;  
        $this->searchValue = $searchValue;  
        $this->categories = $categories;  
        $this->categoryValue = $categoryValue;  
        $this->categoryName = $categoryName;  
        $this->categoryLabel = $categoryLabel;  
        $this->formId = $formId;  
        $this->tableRoute = $tableRoute;
        $this->containerId = $containerId; // Add this line
    }  
  
    public function render()  
    {  
        return view('components.search-filter');  
    }  
}