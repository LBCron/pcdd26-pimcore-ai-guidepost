<?php
declare(strict_types=1);

/**
 * Creates the BlogPost class definition (content model in the database) and sample
 * posts for the CKEditor Studio bundle demo. PCDD 2026 demo code.
 */

namespace App\Command;

use Carbon\Carbon;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ckeditor-studio:install-blog-demo',
    description: 'Creates the BlogPost class (title, slug, publishDate, teaser, content wysiwyg) and sample posts'
)]
final class InstallBlogDemoCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = ClassDefinition::getByName('BlogPost');
        if ($class instanceof ClassDefinition) {
            $output->writeln('Class BlogPost already exists, skipping class creation.');
        } else {
            $class = $this->createClassDefinition();
            $output->writeln('Created class BlogPost.');
        }

        $folder = $this->ensureBlogFolder();
        $created = $this->createSamplePosts($folder, $output);
        $output->writeln(sprintf('Done. %d sample post(s) created. Open /blog on the frontend.', $created));

        return Command::SUCCESS;
    }

    private function createClassDefinition(): ClassDefinition
    {
        $title = new ClassDefinition\Data\Input();
        $title->setName('title');
        $title->setTitle('Title');
        $title->setMandatory(true);

        $slug = new ClassDefinition\Data\Input();
        $slug->setName('slug');
        $slug->setTitle('Slug');
        $slug->setMandatory(true);

        $publishDate = new ClassDefinition\Data\Date();
        $publishDate->setName('publishDate');
        $publishDate->setTitle('Publish date');

        $teaser = new ClassDefinition\Data\Textarea();
        $teaser->setName('teaser');
        $teaser->setTitle('Teaser');

        $content = new ClassDefinition\Data\Wysiwyg();
        $content->setName('content');
        $content->setTitle('Content');

        $panel = new ClassDefinition\Layout\Panel();
        $panel->setName('Blog post');
        $panel->addChild($title);
        $panel->addChild($slug);
        $panel->addChild($publishDate);
        $panel->addChild($teaser);
        $panel->addChild($content);

        $class = new ClassDefinition();
        $class->setName('BlogPost');
        $class->setGroup('Blog');
        $class->setLayoutDefinitions($panel);
        $class->save();

        return $class;
    }

    private function ensureBlogFolder(): DataObject\Folder
    {
        $folder = DataObject\Folder::getByPath('/blog');
        if ($folder instanceof DataObject\Folder) {
            return $folder;
        }

        $folder = new DataObject\Folder();
        $folder->setKey('blog');
        $folder->setParentId(1);
        $folder->save();

        return $folder;
    }

    private function createSamplePosts(DataObject\Folder $folder, OutputInterface $output): int
    {
        $samples = [
            [
                'key' => 'hello-ckeditor',
                'title' => 'Hello CKEditor in Pimcore Studio',
                'slug' => 'hello-ckeditor',
                'teaser' => 'The wysiwyg field of this post is edited with CKEditor inside Pimcore Studio.',
                'content' => '<h2>Why a content model?</h2><p>This post is a <strong>BlogPost data object</strong>: '
                    . 'its structure (title, slug, date, teaser, rich text content) lives in the database as a '
                    . 'Pimcore class definition, not in a page template.</p><p>The <em>content</em> field is a '
                    . 'wysiwyg field, edited with <strong>CKEditor</strong> in Studio and rendered here as HTML.</p>'
                    . '<blockquote><p>Structured content first, editing experience second, both pluggable.</p></blockquote>',
            ],
            [
                'key' => 'structured-content-in-pimcore',
                'title' => 'Structured content in Pimcore',
                'slug' => 'structured-content-in-pimcore',
                'teaser' => 'Listing and detail pages are plain Symfony controllers over the data object listing API.',
                'content' => '<h2>From model to page</h2><p>The listing page queries '
                    . '<code>DataObject\\BlogPost\\Listing</code> ordered by publish date; the detail page resolves '
                    . 'the post by its slug field.</p><ul><li>One content model</li><li>Any number of outputs</li>'
                    . '<li>An editor your authors already know</li></ul>',
            ],
        ];

        $className = 'Pimcore\\Model\\DataObject\\BlogPost';
        $created = 0;

        foreach ($samples as $sample) {
            $existing = DataObject::getByPath('/blog/' . $sample['key']);
            if ($existing !== null) {
                $output->writeln(sprintf('Sample post %s already exists, skipping.', $sample['key']));
                continue;
            }

            /** @var DataObject\Concrete $post */
            $post = new $className();
            $post->setKey($sample['key']);
            $post->setParent($folder);
            $post->setPublished(true);
            $post->set('title', $sample['title']);
            $post->set('slug', $sample['slug']);
            $post->set('publishDate', Carbon::now());
            $post->set('teaser', $sample['teaser']);
            $post->set('content', $sample['content']);
            $post->save();
            $created++;
        }

        return $created;
    }
}
