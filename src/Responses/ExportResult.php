<?php

namespace IVideon\Responses;

use LazyJsonMapper\LazyJsonMapper;

/**
 * ExportResult.
 *
 * @method string getCamera()
 * @method string getCreatedAt()
 * @method string getEndTime()
 * @method int getFileSize()
 * @method string getId()
 * @method string getPreviewUrl()
 * @method int getProgress()
 * @method string getStartTime()
 * @method string getStatus()
 * @method string getVideoUrl()
 * @method bool isCamera()
 * @method bool isCreatedAt()
 * @method bool isEndTime()
 * @method bool isFileSize()
 * @method bool isId()
 * @method bool isPreviewUrl()
 * @method bool isProgress()
 * @method bool isStartTime()
 * @method bool isStatus()
 * @method bool isVideoUrl()
 * @method $this setCamera(string $value)
 * @method $this setCreatedAt(string $value)
 * @method $this setEndTime(string $value)
 * @method $this setFileSize(int $value)
 * @method $this setId(string $value)
 * @method $this setPreviewUrl(string $value)
 * @method $this setProgress(int $value)
 * @method $this setStartTime(string $value)
 * @method $this setStatus(string $value)
 * @method $this setVideoUrl(string $value)
 * @method $this unsetCamera()
 * @method $this unsetCreatedAt()
 * @method $this unsetEndTime()
 * @method $this unsetFileSize()
 * @method $this unsetId()
 * @method $this unsetPreviewUrl()
 * @method $this unsetProgress()
 * @method $this unsetStartTime()
 * @method $this unsetStatus()
 * @method $this unsetVideoUrl()
 *
 * @property string $camera
 * @property string $created_at
 * @property string $end_time
 * @property int $file_size
 * @property string $id
 * @property string $preview_url
 * @property int $progress
 * @property string $start_time
 * @property string $status
 * @property string $video_url
 */
class ExportResult extends LazyJsonMapper
{
    const EXPORT_STATUS_READY = 'ready';
    const EXPORT_STATUS_IN_QUEUE = 'in_queue';

    const JSON_PROPERTY_MAP = [
        'camera'      => 'string',
        'created_at'  => 'string',
        'end_time'    => 'string',
        'file_size'   => 'int',
        'id'          => 'string',
        'preview_url' => 'string',
        'progress'    => 'int',
        'start_time'  => 'string',
        'status'      => 'string',
        'video_url'   => 'string',
    ];
}
