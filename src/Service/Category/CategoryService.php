<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Category;

use AlperRagib\Ticimax\Model\Category\CategoryModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class CategoryService
 * Handles category-related API operations.
 */
class CategoryService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch categories from the API.
     *
     * @param int $categoryId   (optional) The specific category ID to fetch. Defaults to 0 for all categories.
     * @param string|null $language (optional) The language code (e.g., 'en', 'tr'). Null or omitted for default.
     * @param int|null $parentId (optional) The parent category ID. Null or omitted for top-level categories.
     * @return ApiResponse
     */
    public function getCategories(int $categoryId = 0, ?string $language = null, ?int $parentId = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $categories = [];

        try {
            $params = [
                'UyeKodu'    => $this->request->key,
                'kategoriID' => $categoryId,
                'dil'        => $language,
                'parentID'   => $parentId,
            ];

            $response = $client->__soapCall("SelectKategori", [
                'parameters' => $params
            ]);

            $catArr = $response->SelectKategoriResult->Kategori ?? [];
            if (is_object($catArr)) {
                $catArr = [$catArr];
            }

            foreach ($catArr as $cat) {
                $categories[] = new CategoryModel($cat);
            }
            
            return ApiResponse::success(
                $categories,
                'Categories retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving categories: ' . $e->getMessage());
        }
    }

    /**
     * Create a new category via the API.
     * @param CategoryModel $category
     * @return ApiResponse
     */
    public function createCategory(CategoryModel $category): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $params = [
                'UyeKodu'  => $this->request->key,
                'kategori' => $category->toArray(),
            ];
            $response = $client->__soapCall("SaveKategori", [
                'parameters' => $params
            ]);
            
            if (isset($response->SaveKategoriResult)) {
                return ApiResponse::success($response->SaveKategoriResult, 'Category saved successfully.');
            }
            
            return ApiResponse::error('Failed to save category.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Error saving category: ' . $e->getMessage());
        }
    }
}
