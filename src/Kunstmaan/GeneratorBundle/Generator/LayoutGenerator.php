<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates all layout files
 */
class LayoutGenerator extends KunstmaanGenerator
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $shortBundleName;

    /**
     * @var bool
     */
    private $demosite;

    /**
     * Generate the basic layout.
     *
     * @param BundleInterface $bundle  The bundle
     * @param string          $rootDir The root directory of the application
     */
    public function generate(BundleInterface $bundle, $rootDir, $demosite)
    {
        $this->bundle   = $bundle;
        $this->rootDir  = $rootDir;
        $this->demosite = $demosite;

        $this->shortBundleName = '@'.str_replace('Bundle', '', $bundle->getName());

        if ($this->demosite) {
            $this->generateBowerFiles();
        }
        $this->generateGulpFiles();
        $this->generateGemsFile();
        $this->generateAssets();
        $this->generateTemplate();
    }

    /**
     * Generate the bower configuration files.
     */
    private function generateBowerFiles()
    {
        $this->renderFiles(
            $this->skeletonDir . '/bower/',
            $this->rootDir,
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/bower/',
            $this->rootDir,
            '.bowerrc',
            array('bundle' => $this->bundle),
            true
        );
        $this->assistant->writeLine('Generating bower configuration : <info>OK</info>');
    }

    /**
     * Generate the gulp configuration files.
     */
    private function generateGulpFiles()
    {
        $this->renderFiles($this->skeletonDir . '/gulp/', $this->rootDir, array('bundle' => $this->bundle, 'demosite' => $this->demosite), true);
        $this->renderSingleFile(
            $this->skeletonDir . '/gulp/',
            $this->rootDir,
            '.jshintrc',
            array('bundle' => $this->bundle),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/gulp/',
            $this->rootDir,
            '.groundcontrolrc',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->assistant->writeLine('Generating gulp configuration : <info>OK</info>');
    }

    /**
     * Generate the gems configuration file.
     */
    private function generateGemsFile()
    {
        $this->renderFiles($this->skeletonDir . '/gems/', $this->rootDir, array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating gems configuration : <info>OK</info>');
    }

    /**
     * Generate the ui asset files.
     */
    private function generateAssets()
    {
        $sourceDir = $this->skeletonDir;
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/ui/';
        $this->copyFiles($sourceDir . $relPath, $targetDir . $relPath, true);
        $this->renderFiles(
            $sourceDir . $relPath . '/js/',
            $targetDir . $relPath . '/js/',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->renderFiles(
            $sourceDir . $relPath . '/scss/',
            $targetDir . $relPath . '/scss/',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->renderFiles(
            $sourceDir . $relPath . '/styleguide/',
            $targetDir . $relPath . '/styleguide/',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );

        if (!$this->demosite) {

            // Files
            $this->removeDirectory($targetDir . $relPath . '/files/');


            // Images
            $this->removeDirectory($targetDir . $relPath . '/fonts/');


            // JS
            $this->removeFile($targetDir . $relPath . '/js/search.js');
            $this->removeFile($targetDir . $relPath . '/js/demoMsg.js');


            // Images
            $this->removeDirectory($targetDir . $relPath . '/img/demosite/');
            $this->removeDirectory($targetDir . $relPath . '/img/buttons/');
            $this->removeDirectory($targetDir . $relPath . '/img/dummy/');
            $this->removeDirectory($targetDir . $relPath . '/img/backgrounds/');


            // SCSS
            // SCSS - Blocks
            $this->removeFile($targetDir . $relPath . '/scss/components/blocks/_img-icon.scss');

            // SCSS - Forms
            $this->removeFile($targetDir . $relPath . '/scss/components/forms/_form-widget.scss');

            // SCSS - Structures
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_splash.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_submenu.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_blog-item.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_search-results.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_breadcrumb-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_header-visual.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_newsletter.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_pagination.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_demosite-msg.scss');

            // SCSS - Header
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_main-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_site-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_language-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_contact-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_search-form.scss');

            // SCSS - Footer
            $this->removeFile($targetDir . $relPath . '/scss/components/footer/_social-footer.scss');

            // SCSS - Pageparts
            $this->removeFile($targetDir . $relPath . '/scss/components/pageparts/_service-pp.scss');

            // SCSS - Config
            $this->removeFile($targetDir . $relPath . '/scss/config/vendors/_cargobay-imports.scss');
            $this->removeFile($targetDir . $relPath . '/scss/config/vendors/_cargobay-vars.scss');

            // SCSS - Mixins
            $this->removeDirectory($targetDir . $relPath . '/scss/helpers/mixins/');
        }

        $this->assistant->writeLine('Generating ui assets : <info>OK</info>');
    }

    /**
     * Generate the twig template files.
     */
    private function generateTemplate()
    {
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/views/';
        $this->renderFiles(
            $this->skeletonDir . $relPath,
            $this->bundle->getPath() . $relPath,
            array('bundle' => $this->bundle, 'demosite' => $this->demosite, 'shortBundleName' => $this->shortBundleName),
            true
        );

        if (!$this->demosite) {
            // Layout
            $this->removeFile($targetDir . $relPath . '/Layout/_mobile-nav.html.twig');
            $this->removeFile($targetDir . $relPath . '/Layout/_demositemessage.html.twig');
        }

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }
}
