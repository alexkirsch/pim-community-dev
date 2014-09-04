<?php

namespace spec\Pim\Bundle\PdfGeneratorBundle\Renderer;

use Pim\Bundle\CatalogBundle\Model\AbstractProduct;
use Pim\Bundle\PdfGeneratorBundle\Renderer\RendererInterface;
use Pim\Bundle\PdfGeneratorBundle\Exception\RendererRequiredException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RendererRegistrySpec extends ObjectBehavior
{
    function let()
    {
    }

    function it_render_a_document_with_the_right_renderer(RendererInterface $productPdfRenderer, AbstractProduct $blender)
    {
        $this->addRenderer($productPdfRenderer);

        $productPdfRenderer->supports($blender, 'plain')->willReturn(true);
        $productPdfRenderer->render($blender, 'plain', [])->willReturn('I am a nice blender !');

        $this->render($blender, 'plain', [])->shouldReturn('I am a nice blender !');
    }

    function it_render_a_document_with_the_renderer_which_have_the_higher_priority(RendererInterface $productPdfRenderer, RendererInterface $betterProductPdfRenderer, AbstractProduct $blender)
    {
        $this->addRenderer($betterProductPdfRenderer);
        $this->addRenderer($productPdfRenderer);

        $productPdfRenderer->supports($blender, 'plain')->willReturn(true);

        $betterProductPdfRenderer->supports($blender, 'plain')->willReturn(true);
        $betterProductPdfRenderer->render($blender, 'plain', [])->willReturn('I am a nicer blender !');

        $this->render($blender, 'plain', [])->shouldReturn('I am a nicer blender !');
    }

    function it_render_a_document_with_the_renderer_which_is_compatible(RendererInterface $productPdfRenderer, RendererInterface $betterProductPdfRenderer, AbstractProduct $blender)
    {
        $this->addRenderer($betterProductPdfRenderer);
        $this->addRenderer($productPdfRenderer);

        $productPdfRenderer->supports($blender, 'plain')->willReturn(true);
        $productPdfRenderer->render($blender, 'plain', [])->willReturn('I am a nice blender !');

        $betterProductPdfRenderer->supports($blender, 'plain')->willReturn(false);

        $this->render($blender, 'plain', [])->shouldReturn('I am a nice blender !');
    }

    function it_throw_an_exception_If_there_is_no_renderer_available(RendererInterface $productPdfRenderer, RendererInterface $betterProductPdfRenderer, AbstractProduct $blender)
    {
        $this->addRenderer($betterProductPdfRenderer);
        $this->addRenderer($productPdfRenderer);

        $productPdfRenderer->supports($blender, 'plain')->willReturn(false);
        $betterProductPdfRenderer->supports($blender, 'plain')->willReturn(false);

        $blender->__toString()->willReturn('I am a unrendered product');

        $this->shouldThrow('Pim\Bundle\PdfGeneratorBundle\Exception\RendererRequiredException')->during('render', [$blender, 'plain', []]);
    }
}
