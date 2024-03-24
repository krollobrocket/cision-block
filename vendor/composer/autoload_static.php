<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7f6eedbbf8dad5c5aa84803e2be4c706
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
            'CisionBlock\\Psr\\Http\\Message\\' => 29,
            'CisionBlock\\Psr\\Http\\Client\\' => 28,
            'CisionBlock\\GuzzleHttp\\Psr7\\' => 28,
            'CisionBlock\\GuzzleHttp\\Promise\\' => 31,
            'CisionBlock\\GuzzleHttp\\' => 23,
            'CisionBlock\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'CisionBlock\\Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src',
            1 => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src',
        ),
        'CisionBlock\\Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Vendor/psr/http-client/src',
        ),
        'CisionBlock\\GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src',
        ),
        'CisionBlock\\GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src',
        ),
        'CisionBlock\\GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src',
        ),
        'CisionBlock\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'CisionBlock\\Addon\\AbstractAddon' => __DIR__ . '/../..' . '/src/Addon/AbstractAddon.php',
        'CisionBlock\\Addon\\AddonInterface' => __DIR__ . '/../..' . '/src/Addon/AddonInterface.php',
        'CisionBlock\\Backend\\Backend' => __DIR__ . '/../..' . '/src/Backend/Backend.php',
        'CisionBlock\\Frontend\\Frontend' => __DIR__ . '/../..' . '/src/Frontend/Frontend.php',
        'CisionBlock\\GuzzleHttp\\BodySummarizer' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/BodySummarizer.php',
        'CisionBlock\\GuzzleHttp\\BodySummarizerInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/BodySummarizerInterface.php',
        'CisionBlock\\GuzzleHttp\\Client' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Client.php',
        'CisionBlock\\GuzzleHttp\\ClientInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/ClientInterface.php',
        'CisionBlock\\GuzzleHttp\\ClientTrait' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/ClientTrait.php',
        'CisionBlock\\GuzzleHttp\\Cookie\\CookieJar' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Cookie/CookieJar.php',
        'CisionBlock\\GuzzleHttp\\Cookie\\CookieJarInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php',
        'CisionBlock\\GuzzleHttp\\Cookie\\FileCookieJar' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Cookie/FileCookieJar.php',
        'CisionBlock\\GuzzleHttp\\Cookie\\SessionCookieJar' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Cookie/SessionCookieJar.php',
        'CisionBlock\\GuzzleHttp\\Cookie\\SetCookie' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Cookie/SetCookie.php',
        'CisionBlock\\GuzzleHttp\\Exception\\BadResponseException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/BadResponseException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\ClientException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/ClientException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\ConnectException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/ConnectException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\GuzzleException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/GuzzleException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\InvalidArgumentException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/InvalidArgumentException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\RequestException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/RequestException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\ServerException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/ServerException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\TooManyRedirectsException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/TooManyRedirectsException.php',
        'CisionBlock\\GuzzleHttp\\Exception\\TransferException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Exception/TransferException.php',
        'CisionBlock\\GuzzleHttp\\HandlerStack' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/HandlerStack.php',
        'CisionBlock\\GuzzleHttp\\Handler\\CurlFactory' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
        'CisionBlock\\GuzzleHttp\\Handler\\CurlFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
        'CisionBlock\\GuzzleHttp\\Handler\\CurlHandler' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
        'CisionBlock\\GuzzleHttp\\Handler\\CurlMultiHandler' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
        'CisionBlock\\GuzzleHttp\\Handler\\EasyHandle' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
        'CisionBlock\\GuzzleHttp\\Handler\\HeaderProcessor' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/HeaderProcessor.php',
        'CisionBlock\\GuzzleHttp\\Handler\\MockHandler' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/MockHandler.php',
        'CisionBlock\\GuzzleHttp\\Handler\\Proxy' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/Proxy.php',
        'CisionBlock\\GuzzleHttp\\Handler\\StreamHandler' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
        'CisionBlock\\GuzzleHttp\\MessageFormatter' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/MessageFormatter.php',
        'CisionBlock\\GuzzleHttp\\MessageFormatterInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/MessageFormatterInterface.php',
        'CisionBlock\\GuzzleHttp\\Middleware' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Middleware.php',
        'CisionBlock\\GuzzleHttp\\Pool' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Pool.php',
        'CisionBlock\\GuzzleHttp\\PrepareBodyMiddleware' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
        'CisionBlock\\GuzzleHttp\\Promise\\AggregateException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/AggregateException.php',
        'CisionBlock\\GuzzleHttp\\Promise\\CancellationException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/CancellationException.php',
        'CisionBlock\\GuzzleHttp\\Promise\\Coroutine' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/Coroutine.php',
        'CisionBlock\\GuzzleHttp\\Promise\\Create' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/Create.php',
        'CisionBlock\\GuzzleHttp\\Promise\\Each' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/Each.php',
        'CisionBlock\\GuzzleHttp\\Promise\\EachPromise' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/EachPromise.php',
        'CisionBlock\\GuzzleHttp\\Promise\\FulfilledPromise' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/FulfilledPromise.php',
        'CisionBlock\\GuzzleHttp\\Promise\\Is' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/Is.php',
        'CisionBlock\\GuzzleHttp\\Promise\\Promise' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/Promise.php',
        'CisionBlock\\GuzzleHttp\\Promise\\PromiseInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/PromiseInterface.php',
        'CisionBlock\\GuzzleHttp\\Promise\\PromisorInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/PromisorInterface.php',
        'CisionBlock\\GuzzleHttp\\Promise\\RejectedPromise' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/RejectedPromise.php',
        'CisionBlock\\GuzzleHttp\\Promise\\RejectionException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/RejectionException.php',
        'CisionBlock\\GuzzleHttp\\Promise\\TaskQueue' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/TaskQueue.php',
        'CisionBlock\\GuzzleHttp\\Promise\\TaskQueueInterface' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/TaskQueueInterface.php',
        'CisionBlock\\GuzzleHttp\\Promise\\Utils' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/promises/src/Utils.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\AppendStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/AppendStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\BufferStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/BufferStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\CachingStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/CachingStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\DroppingStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/DroppingStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Exception\\MalformedUriException' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Exception/MalformedUriException.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\FnStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/FnStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Header' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Header.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\HttpFactory' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/HttpFactory.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\InflateStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/InflateStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\LazyOpenStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/LazyOpenStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\LimitStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/LimitStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Message' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Message.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\MessageTrait' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/MessageTrait.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\MimeType' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/MimeType.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\MultipartStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/MultipartStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\NoSeekStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/NoSeekStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\PumpStream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/PumpStream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Query' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Query.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Request' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Request.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Response' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Response.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Rfc7230' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Rfc7230.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\ServerRequest' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/ServerRequest.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Stream' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Stream.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\StreamDecoratorTrait' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/StreamDecoratorTrait.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\StreamWrapper' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/StreamWrapper.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\UploadedFile' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/UploadedFile.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Uri' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Uri.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\UriComparator' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/UriComparator.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\UriNormalizer' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/UriNormalizer.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\UriResolver' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/UriResolver.php',
        'CisionBlock\\GuzzleHttp\\Psr7\\Utils' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/psr7/src/Utils.php',
        'CisionBlock\\GuzzleHttp\\RedirectMiddleware' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/RedirectMiddleware.php',
        'CisionBlock\\GuzzleHttp\\RequestOptions' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/RequestOptions.php',
        'CisionBlock\\GuzzleHttp\\RetryMiddleware' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/RetryMiddleware.php',
        'CisionBlock\\GuzzleHttp\\TransferStats' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/TransferStats.php',
        'CisionBlock\\GuzzleHttp\\Utils' => __DIR__ . '/../..' . '/src/Vendor/guzzlehttp/guzzle/src/Utils.php',
        'CisionBlock\\Locale\\Locale' => __DIR__ . '/../..' . '/src/Locale/Locale.php',
        'CisionBlock\\Plugin\\Common\\Singleton' => __DIR__ . '/../..' . '/src/Plugin/Common/Singleton.php',
        'CisionBlock\\Plugin\\Settings\\Settings' => __DIR__ . '/../..' . '/src/Plugin/Settings/Settings.php',
        'CisionBlock\\Plugin\\Widget\\Widget' => __DIR__ . '/../..' . '/src/Plugin/Widget/Widget.php',
        'CisionBlock\\Psr\\Http\\Client\\ClientExceptionInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-client/src/ClientExceptionInterface.php',
        'CisionBlock\\Psr\\Http\\Client\\ClientInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-client/src/ClientInterface.php',
        'CisionBlock\\Psr\\Http\\Client\\NetworkExceptionInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-client/src/NetworkExceptionInterface.php',
        'CisionBlock\\Psr\\Http\\Client\\RequestExceptionInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-client/src/RequestExceptionInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\MessageInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/MessageInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\RequestFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src/RequestFactoryInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\RequestInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/RequestInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\ResponseFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src/ResponseFactoryInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\ResponseInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/ResponseInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\ServerRequestFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src/ServerRequestFactoryInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\ServerRequestInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/ServerRequestInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\StreamFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src/StreamFactoryInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\StreamInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/StreamInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\UploadedFileFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src/UploadedFileFactoryInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\UploadedFileInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/UploadedFileInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\UriFactoryInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-factory/src/UriFactoryInterface.php',
        'CisionBlock\\Psr\\Http\\Message\\UriInterface' => __DIR__ . '/../..' . '/src/Vendor/psr/http-message/src/UriInterface.php',
        'CisionBlock\\Settings\\Settings' => __DIR__ . '/../..' . '/src/Settings/Settings.php',
        'CisionBlock\\Trait\\AddonTrait' => __DIR__ . '/../..' . '/src/Trait/AddonTrait.php',
        'CisionBlock\\Widget\\Widget' => __DIR__ . '/../..' . '/src/Widget/Widget.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Composer\\Installers\\AglInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/AglInstaller.php',
        'Composer\\Installers\\AkauntingInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/AkauntingInstaller.php',
        'Composer\\Installers\\AnnotateCmsInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/AnnotateCmsInstaller.php',
        'Composer\\Installers\\AsgardInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/AsgardInstaller.php',
        'Composer\\Installers\\AttogramInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/AttogramInstaller.php',
        'Composer\\Installers\\BaseInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/BaseInstaller.php',
        'Composer\\Installers\\BitrixInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/BitrixInstaller.php',
        'Composer\\Installers\\BonefishInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/BonefishInstaller.php',
        'Composer\\Installers\\CakePHPInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/CakePHPInstaller.php',
        'Composer\\Installers\\ChefInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ChefInstaller.php',
        'Composer\\Installers\\CiviCrmInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/CiviCrmInstaller.php',
        'Composer\\Installers\\ClanCatsFrameworkInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ClanCatsFrameworkInstaller.php',
        'Composer\\Installers\\CockpitInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/CockpitInstaller.php',
        'Composer\\Installers\\CodeIgniterInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/CodeIgniterInstaller.php',
        'Composer\\Installers\\Concrete5Installer' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/Concrete5Installer.php',
        'Composer\\Installers\\CroogoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/CroogoInstaller.php',
        'Composer\\Installers\\DecibelInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/DecibelInstaller.php',
        'Composer\\Installers\\DframeInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/DframeInstaller.php',
        'Composer\\Installers\\DokuWikiInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/DokuWikiInstaller.php',
        'Composer\\Installers\\DolibarrInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/DolibarrInstaller.php',
        'Composer\\Installers\\DrupalInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/DrupalInstaller.php',
        'Composer\\Installers\\ElggInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ElggInstaller.php',
        'Composer\\Installers\\EliasisInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/EliasisInstaller.php',
        'Composer\\Installers\\ExpressionEngineInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ExpressionEngineInstaller.php',
        'Composer\\Installers\\EzPlatformInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/EzPlatformInstaller.php',
        'Composer\\Installers\\FuelInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/FuelInstaller.php',
        'Composer\\Installers\\FuelphpInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/FuelphpInstaller.php',
        'Composer\\Installers\\GravInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/GravInstaller.php',
        'Composer\\Installers\\HuradInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/HuradInstaller.php',
        'Composer\\Installers\\ImageCMSInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ImageCMSInstaller.php',
        'Composer\\Installers\\Installer' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/Installer.php',
        'Composer\\Installers\\ItopInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ItopInstaller.php',
        'Composer\\Installers\\KanboardInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/KanboardInstaller.php',
        'Composer\\Installers\\KnownInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/KnownInstaller.php',
        'Composer\\Installers\\KodiCMSInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/KodiCMSInstaller.php',
        'Composer\\Installers\\KohanaInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/KohanaInstaller.php',
        'Composer\\Installers\\LanManagementSystemInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/LanManagementSystemInstaller.php',
        'Composer\\Installers\\LaravelInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/LaravelInstaller.php',
        'Composer\\Installers\\LavaLiteInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/LavaLiteInstaller.php',
        'Composer\\Installers\\LithiumInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/LithiumInstaller.php',
        'Composer\\Installers\\MODULEWorkInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MODULEWorkInstaller.php',
        'Composer\\Installers\\MODXEvoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MODXEvoInstaller.php',
        'Composer\\Installers\\MagentoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MagentoInstaller.php',
        'Composer\\Installers\\MajimaInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MajimaInstaller.php',
        'Composer\\Installers\\MakoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MakoInstaller.php',
        'Composer\\Installers\\MantisBTInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MantisBTInstaller.php',
        'Composer\\Installers\\MatomoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MatomoInstaller.php',
        'Composer\\Installers\\MauticInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MauticInstaller.php',
        'Composer\\Installers\\MayaInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MayaInstaller.php',
        'Composer\\Installers\\MediaWikiInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MediaWikiInstaller.php',
        'Composer\\Installers\\MiaoxingInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MiaoxingInstaller.php',
        'Composer\\Installers\\MicroweberInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MicroweberInstaller.php',
        'Composer\\Installers\\ModxInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ModxInstaller.php',
        'Composer\\Installers\\MoodleInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/MoodleInstaller.php',
        'Composer\\Installers\\OctoberInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/OctoberInstaller.php',
        'Composer\\Installers\\OntoWikiInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/OntoWikiInstaller.php',
        'Composer\\Installers\\OsclassInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/OsclassInstaller.php',
        'Composer\\Installers\\OxidInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/OxidInstaller.php',
        'Composer\\Installers\\PPIInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PPIInstaller.php',
        'Composer\\Installers\\PantheonInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PantheonInstaller.php',
        'Composer\\Installers\\PhiftyInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PhiftyInstaller.php',
        'Composer\\Installers\\PhpBBInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PhpBBInstaller.php',
        'Composer\\Installers\\PiwikInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PiwikInstaller.php',
        'Composer\\Installers\\PlentymarketsInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PlentymarketsInstaller.php',
        'Composer\\Installers\\Plugin' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/Plugin.php',
        'Composer\\Installers\\PortoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PortoInstaller.php',
        'Composer\\Installers\\PrestashopInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PrestashopInstaller.php',
        'Composer\\Installers\\ProcessWireInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ProcessWireInstaller.php',
        'Composer\\Installers\\PuppetInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PuppetInstaller.php',
        'Composer\\Installers\\PxcmsInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/PxcmsInstaller.php',
        'Composer\\Installers\\RadPHPInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/RadPHPInstaller.php',
        'Composer\\Installers\\ReIndexInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ReIndexInstaller.php',
        'Composer\\Installers\\Redaxo5Installer' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/Redaxo5Installer.php',
        'Composer\\Installers\\RedaxoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/RedaxoInstaller.php',
        'Composer\\Installers\\RoundcubeInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/RoundcubeInstaller.php',
        'Composer\\Installers\\SMFInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/SMFInstaller.php',
        'Composer\\Installers\\ShopwareInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ShopwareInstaller.php',
        'Composer\\Installers\\SilverStripeInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/SilverStripeInstaller.php',
        'Composer\\Installers\\SiteDirectInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/SiteDirectInstaller.php',
        'Composer\\Installers\\StarbugInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/StarbugInstaller.php',
        'Composer\\Installers\\SyDESInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/SyDESInstaller.php',
        'Composer\\Installers\\SyliusInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/SyliusInstaller.php',
        'Composer\\Installers\\TaoInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/TaoInstaller.php',
        'Composer\\Installers\\TastyIgniterInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/TastyIgniterInstaller.php',
        'Composer\\Installers\\TheliaInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/TheliaInstaller.php',
        'Composer\\Installers\\TuskInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/TuskInstaller.php',
        'Composer\\Installers\\UserFrostingInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/UserFrostingInstaller.php',
        'Composer\\Installers\\VanillaInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/VanillaInstaller.php',
        'Composer\\Installers\\VgmcpInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/VgmcpInstaller.php',
        'Composer\\Installers\\WHMCSInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/WHMCSInstaller.php',
        'Composer\\Installers\\WinterInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/WinterInstaller.php',
        'Composer\\Installers\\WolfCMSInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/WolfCMSInstaller.php',
        'Composer\\Installers\\WordPressInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/WordPressInstaller.php',
        'Composer\\Installers\\YawikInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/YawikInstaller.php',
        'Composer\\Installers\\ZendInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ZendInstaller.php',
        'Composer\\Installers\\ZikulaInstaller' => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers/ZikulaInstaller.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7f6eedbbf8dad5c5aa84803e2be4c706::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7f6eedbbf8dad5c5aa84803e2be4c706::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7f6eedbbf8dad5c5aa84803e2be4c706::$classMap;

        }, null, ClassLoader::class);
    }
}
