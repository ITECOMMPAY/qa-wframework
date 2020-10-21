<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.02.19
 * Time: 18:01
 */

namespace Common\Module\WFramework\Logger\HtmlLogger;

use Monolog\Formatter\NormalizerFormatter;
use function str_replace;
use function strtolower;
use function trim;

class CustomHtmlFormatter extends NormalizerFormatter
{
    /**
     * @param string $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct($dateFormat = null)
    {
        parent::__construct($dateFormat);
    }

    protected function addRecord(array $record)
    {
        $levelName = strtolower($record['level_name']);
        $content = htmlentities($record['message'], ENT_QUOTES, 'UTF-8');
        $content = trim(str_replace(PHP_EOL, '<br>', $content));
        $screenshot = $record['context']['screenshot_filename'] ?? '';

        $accordion = '';

        if ($levelName === 'info' || $levelName === 'notice')
        {
            $accordion = ' accordion';
        }

        return <<<EOT
        
<div class="$levelName$accordion" data-shot="$screenshot">
    <p>$content</p>
</div>

EOT;
    }

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        return $this->addRecord($record);
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }
}
