<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\User;

use AlperRagib\Ticimax\Model\User\UserModel;
use AlperRagib\Ticimax\Model\User\LoginResultModel;
use AlperRagib\Ticimax\Model\User\UserAddressModel;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class UserService
 * Handles user-related API operations.
 */
class UserService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UyeServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch users from the API.
     * @param array $filters
     * @param array $pagination
     * @return UserModel[]
     */
    public function getUsers(array $filters = [], array $pagination = []): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        $users = [];
        try {
            $defaultFilters = [
                'Aktif'                      => -1,
                'AlisverisYapti'             => -1,
                'BakiyeGetir'                => null,
                'Cinsiyet'                   => -1,
                'DogumTarihi1'               => null,
                'DogumTarihi2'               => null,
                'DuzenlemeTarihi1'           => null,
                'DuzenlemeTarihi2'           => null,
                'IlID'                       => 0,
                'IlceID'                     => 0,
                'IzinGuncellemeTarihi1'      => null,
                'IzinGuncellemeTarihi2'      => null,
                'IzinGuncellemeTarihiBas'    => null,
                'IzinGuncellemeTarihiGetir'  => null,
                'IzinGuncellemeTarihiSon'    => null,
                'Mail'                       => '',
                'MailIzin'                   => -1,
                'MusteriKodu'                => '',
                'Onay'                       => null,
                'SmsIzin'                    => -1,
                'SonGirisTarihi1'            => null,
                'SonGirisTarihi2'            => null,
                'Telefon'                    => '',
                'TelefonEsit'                => '',
                'UyeID'                      => 0,
                'UyelikTarihi1'              => null,
                'UyelikTarihi2'              => null,
            ];

            $defaultPagination = [
                'KayitSayisi'               => 20,
                'SayfaNo'                   => 0,
                'SiralamaDegeri'            => 'ID',
                'SiralamaYonu'              => 'DESC',
            ];

            $uyeFiltre = array_merge($defaultFilters, $filters);
            $uyeSayfalama = array_merge($defaultPagination, $pagination);
            $response = $client->__soapCall("SelectUyeler", [
                [
                    'UyeKodu' => $this->request->key,
                    'filtre'       => (object)$uyeFiltre,
                    'sayfalama'       => (object)$uyeSayfalama,
                ]
            ]);

            $uyeler = $response->SelectUyelerResult->Uye ?? [];
            if (is_object($uyeler)) {
                $uyeler = [$uyeler];
            }
            foreach (
                $uyeler as $uye
            ) {
                $users[] = new UserModel($uye);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        return $users;
    }

    /**
     * User login function.
     * @param string $email User email
     * @param string $password User password
     * @param string|null $otp One-time password (optional)
     * @param bool $isAdmin Whether this is an admin login
     * @return LoginResultModel|null
     */
    public function login(string $email, string $password, ?string $otp = null, bool $isAdmin = false): ?LoginResultModel
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $loginData = [
                'Admin' => $isAdmin,
                'Mail' => $email,
                'Sifre' => $password,
                'Otp' => $otp
            ];

            $params = [
                'UyeKodu' => $this->request->key,
                'ug' => (object)$loginData,
            ];

            $response = $client->__soapCall("GirisYap", [
                'parameters' => $params
            ]);

            if (isset($response->GirisYapResult)) {
                return new LoginResultModel($response->GirisYapResult);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }

        return null;
    }

    /**
     * Get user addresses.
     * @param int|null $userId User ID (optional, if not provided will get all addresses)
     * @param int|null $addressId Specific address ID (optional)
     * @return UserAddressModel[]
     */
    public function getUserAddresses(?int $userId = null, ?int $addressId = null): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        $addresses = [];
        
        try {

            $response = $client->__soapCall("SelectUyeAdres", [
                [
                    'UyeKodu' => $this->request->key,
                    'adresId' => $addressId ?? null,
                    'uyeId' => $userId ?? null,
                ]
            ]);

            $uyeAdresler = $response->SelectUyeAdresResult->UyeAdres ?? [];
            if (is_object($uyeAdresler)) {
                $uyeAdresler = [$uyeAdresler];
            }
            
            foreach ($uyeAdresler as $adres) {
                $addresses[] = new UserAddressModel($adres);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        
        return $addresses;
    }

    /**
     * Save user address.
     * @param array $addressData Address data array
     * @return int|null Returns the saved address ID on success, null on failure
     */
    public function saveUserAddress(array $addressData): ?int
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SaveUyeAdres", [
                [
                    'UyeKodu' => $this->request->key,
                    'adres' => (object)$addressData,
                ]
            ]);

            if (isset($response->SaveUyeAdresResult)) {
                return (int)$response->SaveUyeAdresResult;
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        
        return null;
    }

    /**
     * Save user.
     * @param array $userData User data array
     * @param array $userSettings User settings array (optional)
     * @return int|null Returns the saved user ID on success, null on failure
     */
    public function saveUser(array $userData, array $userSettings = []): ?int
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            // Default user settings if not provided
            $defaultSettings = [
                'AlisverissizOdemeGuncelle' => false,
                'CepTelefonuGuncelle' => false,
                'CinsiyetGuncelle' => false,
                'DogumTarihiGuncelle' => false,
                'IlGuncelle' => false,
                'IlceGuncelle' => false,
                'IsimGuncelle' => false,
                'KVKKSozlesmeOnayGuncelle' => false,
                'KapidaOdemeYasaklaGuncelle' => false,
                'KrediLimitiGuncelle' => false,
                'MailGuncelle' => false,
                'MailIzinGuncelle' => false,
                'MeslekGuncelle' => false,
                'MusteriKoduGuncelle' => false,
                'SifreGuncelle' => false,
                'SifreKaydetmeTuru' => null,
                'SmsIzinGuncelle' => false,
                'TelefonGuncelle' => false,
                'UyeSifreyiKendiOlustursun' => false,
                'UyelikSozlesmeOnayGuncelle' => false,
                'UyelikTarihiGuncelle' => false,
                'UyelikTuruGuncelle' => false,
            ];

            $settings = array_merge($defaultSettings, $userSettings);

            $response = $client->__soapCall("SaveUye", [
                [
                    'UyeKodu' => $this->request->key,
                    'u' => (object)$userData,
                    'ayar' => (object)$settings,
                ]
            ]);

            if (isset($response->SaveUyeResult)) {
                return (int)$response->SaveUyeResult;
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        
        return null;
    }


}
