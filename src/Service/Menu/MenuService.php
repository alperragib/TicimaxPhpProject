<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Menu;

use AlperRagib\Ticimax\Model\Menu\MenuModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class MenuService
 * Handles menu-related API operations
 */
class MenuService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/CustomServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get menus from the API
     * @param array $filters Optional filters (Aktif, Dil, MenuID)
     * @return ApiResponse
     */
    public function getMenus(array $filters = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $defaultFilters = [
                'Aktif' => -1,  // -1: all, 0: inactive, 1: active
                'Dil' => '',    // Language code
                'MenuID' => 0   // Specific menu ID, 0 for all
            ];

            $menuRequest = array_merge($defaultFilters, $filters);

            $response = $client->__soapCall("GetMenu", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$menuRequest
                ]
            ]);

            if (isset($response->GetMenuResult)) {
                $result = $response->GetMenuResult;

                // Check for API error
                if ($result->IsError ?? false) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Unknown error occurred');
                }

                // Process menus
                $menus = [];
                if (isset($result->Menuler->WebMenu)) {
                    $menuData = $result->Menuler->WebMenu;
                    
                    // Convert single object to array if needed
                    if (is_object($menuData)) {
                        $menuData = [$menuData];
                    }

                    foreach ($menuData as $menu) {
                        $menus[] = new MenuModel($menu);
                    }
                }

                return ApiResponse::success($menus, 'Menus retrieved successfully.');
            }

            return ApiResponse::success([], 'No menus found.');

        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving menus: ' . $e->getMessage());
        }
    }
    
} 