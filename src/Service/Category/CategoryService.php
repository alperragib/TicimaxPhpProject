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
     * @return CategoryModel[]   Array of CategoryModel objects representing the categories.
     */
    public function getCategories(int $categoryId = 0, ?string $language = null, ?int $parentId = null): array
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
        } catch (SoapFault $e) {
            // Handle or log error
        }

        return $categories;
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
                return ApiResponse::success($response->SaveKategoriResult, 'Kategori başarıyla kaydedildi.');
            }
            
            return ApiResponse::error('Kategori kaydedilemedi.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Kategori kaydedilirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
