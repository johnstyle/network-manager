<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DnsRecord;
use App\Repository\IpRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 */
class DefaultController extends AbstractController
{
    private IpRepository $ipRepository;

    /**
     * DefaultController constructor.
     *
     * @param IpRepository $ipRepository
     */
    public function __construct(IpRepository $ipRepository)
    {
        $this->ipRepository = $ipRepository;
    }

    /**
     * indexAction
     *
     * @Route("/", name="app_home")
     */
    public function indexAction(): Response
    {
        $rows = [];

        foreach ($this->ipRepository->findAll() as $item) {
            $domains = [];
            $dnsReccords = [];
            /** @var DnsRecord $dnsReccord */
            foreach ($item->getDnsRecords() as $dnsReccord) {
                $dnsReccords[] = sprintf(
                    "%s\t%s",
                    $dnsReccord->getType(),
                    $dnsReccord->getName()->getName()
                );
                $domains[] = $dnsReccord->getName()->getDomain()->getName();
            }
            $rows[] = [
                'name' => $item->getName(),
                'dnsReccords' => implode(', ', $dnsReccords),
                'domains' => implode(', ', $domains),
                'route' => $item->getRoute(),
                'source' => $item->getSource(),
                'networkName' => $item->getNetworkName(),
                'networkHandle' => $item->getNetworkHandle(),
                'organizationName' => $item->getOrganizationName(),
                'organizationHandle' => $item->getOrganizationHandle(),
                'asn' => $item->getAsn(),
            ];
        }

        return $this->render('default.html.twig', [
            'columns' => [
                [ 'prop' => 'name', 'name' => 'Name', 'sortable' => true, 'size' => 200, 'pin' => 'colPinStart' ],
                [ 'prop' => 'dnsReccords', 'name' => 'DNS records', 'size' => 500 ],
                [ 'prop' => 'domains', 'name' => 'Domains', 'size' => 200 ],
                [ 'prop' => 'route', 'name' => 'Route', 'sortable' => true, 'size' => 200 ],
                [ 'prop' => 'source', 'name' => 'Source', 'sortable' => true, 'size' => 200 ],
                [ 'prop' => 'networkName', 'name' => 'Network Name', 'sortable' => true, 'size' => 200 ],
                [ 'prop' => 'networkHandle', 'name' => 'Network Handle', 'sortable' => true, 'size' => 200 ],
                [ 'prop' => 'organizationName', 'name' => 'Organization Name', 'sortable' => true, 'size' => 200 ],
                [ 'prop' => 'organizationHandle', 'name' => 'Organization Handle', 'sortable' => true, 'size' => 200 ],
                [ 'prop' => 'asn', 'name' => 'ASN', 'sortable' => true, 'size' => 200 ],
            ],
            'rows' => $rows,
        ]);
    }
}
