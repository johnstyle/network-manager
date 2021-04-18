<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DnsRecord;
use App\Repository\IpRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $defaultPageLength = 50;

        if ($request->isXmlHttpRequest()) {
            $rows = [];

            $columns = [];
            foreach ($request->query->get('columns') as $index => $column) {
                switch ($column['data']) {
                    default:
                        $name = '%s.' . $column['data'];
                        break;
                }
                $columns[$index] = $name;
            }

            $order = [];
            foreach ($request->query->get('order') as $arg) {
                $order[] = [ $columns[$arg['column']], $arg['dir'] ];
            }

            $filters = [
                'start' => (int) $request->query->get('start', 0),
                'length' => (int) $request->query->get('length', $defaultPageLength),
                'search' => $request->query->get('search')['value'] ?? null,
                'order' => $order,
            ];

            $query = $this->ipRepository->search($filters);

            $total = \count($query->getResult());

            $results = $query
                ->setFirstResult($filters['start'])
                ->setMaxResults($filters['length'])
                ->getResult();

            foreach ($results as $item) {
                $domains = [];
                $dnsReccords = [];
                /** @var DnsRecord $dnsReccord */
                foreach ($item->getDnsRecords() as $dnsReccord) {
                    $dnsName = $dnsReccord->getName();
                    $dnsReccords[] = sprintf(
                        "%s\t%s",
                        $dnsReccord->getType(),
                        $dnsName ? $dnsName->getName() : '-'
                    );
                    $domains[] = $dnsName && $dnsName->getDomain() ? $dnsName->getDomain()->getName() : null;
                }
                $rows[] = [
                    'name' => $item->getName(),
                    'dnsReccords' => implode(', ', $dnsReccords),
                    'type' => $item->getType(),
                    'category' => $item->getCategory(),
                    'domains' => implode(', ', $domains),
                    'route' => $item->getRoute(),
                    'registry' => $item->getRegistry(),
                    'organization' => $item->getOrganization(),
                    'country' => $item->getCountry(),
                    'asn' => $item->getAsn(),
                    'allocatedAt' => $item->getAllocatedAt() ? $item->getAllocatedAt()->format('Y-m-d') : '-',
                ];
            }

            return new JsonResponse([
                'draw' => ((int) $request->query->get('draw')) + 1,
                'recordsFiltered' => $total,
                'recordsTotal' => $total,
                'data' => $rows,
            ]);
        }

        return $this->render('default.html.twig', [
            'pageLength' => $defaultPageLength,
            'columns' => [
                [ 'data' => 'name', 'text' => 'Name' ],
                [ 'data' => 'dnsReccords', 'text' => 'DNS records', 'sortable' => false ],
                [ 'data' => 'type', 'text' => 'Type' ],
                [ 'data' => 'category', 'text' => 'Category' ],
                [ 'data' => 'domains', 'text' => 'Domains', 'sortable' => false ],
                [ 'data' => 'route', 'text' => 'Route' ],
                [ 'data' => 'registry', 'text' => 'Registry' ],
                [ 'data' => 'organization', 'text' => 'Organization' ],
                [ 'data' => 'country', 'text' => 'Country' ],
                [ 'data' => 'asn', 'text' => 'ASN' ],
                [ 'data' => 'allocatedAt', 'text' => 'Allocated' ],
            ],
        ]);
    }
}
