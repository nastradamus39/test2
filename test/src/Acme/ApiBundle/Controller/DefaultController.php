<?php

namespace Acme\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\News;

class DefaultController extends Controller
{
    /**
     * @Rest\View()
     */
    public function indexAction(Request $request)
    {

        $page = $request->get('page', 1);
        $onPage = $request->get('onpage', 15);
        $sort = $request->get('order','sort');

        if( !in_array($sort,News::visibleFields() ) ) $sort = 'sort';

        $page = intval($page);
        $onPage = intval($onPage);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:News');

        $totalCount = $repository->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $totalCount = intval($totalCount);

        $query = $repository->createQueryBuilder('p')
            ->select('p.id','p.title','p.preview','p.sort')
            ->orderBy('p.'.$sort, 'ASC')
            ->setMaxResults($onPage)
            ->setFirstResult(($page-1)*$onPage)
            ->getQuery();

        $news = $query->getResult();

        return [
            'method'=>'news',
            'data'=>$news,
            'total'=>$totalCount,
            'page'=>$page,
            'onpage'=>$onPage,
            'orderby'=>$sort
        ];
    }

    /**
     * @Rest\View()
     */
    public function detailAction($id)
    {

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:News');

        $query = $repository->createQueryBuilder('p')
            ->select()
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return [
            'method'=>'news/id',
            'data'=>$query->getResult()
        ];
    }
}
