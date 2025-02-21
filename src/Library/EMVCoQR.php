<?php

namespace phumin\PromptParse\Library;

class EMVCoQR
{
  private $payload;
  /**
   * 
   * @var TLV[]
   */
  private $tags = [];

  public function __construct(string $payload, array $tags = [])
  {
    $this->payload = $payload;
    $this->tags = $tags;
  }

  public function getPayload()
  {
    return $this->payload;
  }

  public function getTags()
  {
    return $this->tags;
  }

  public function getTag($tagId, $subTagId = null)
  {
    foreach ($this->tags as $tag) {
      if ($tag->id == $tagId) {
        if ($subTagId !== null) {
          foreach ($tag->subTags as $subTag) {
            if ($subTag->id == $subTagId) return $subTag;
          }

          return false;
        }

        return $tag;
      }
    }

    return false;
  }

  public function getTagValue($tagId, $subTagId = null)
  {
    $tag = $this->getTag($tagId, $subTagId);

    if (!$tag) return false;
    return $tag->value;
  }
}
