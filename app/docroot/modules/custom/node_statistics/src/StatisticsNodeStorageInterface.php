<?php

namespace Drupal\cms_statistics;

/**
 * Interface CmsStatisticsRedisInterface.
 */
interface StatisticsNodeStorageInterface {

  /**
   * Get all keys counter entity exited.
   *
   * @return array
   *   List of keys counter entity.
   */
  public function getCurrentKeys();

  /**
   * Count a entity view.
   *
   * @param int $id
   *   The ID of the entity to count.
   *
   * @return bool
   *   TRUE if the entity view has been counted.
   */
  public function recordView($id);

  /**
   * Returns the number of times entities have been viewed.
   *
   * @param array $keys
   *   List keys store the number of times entities have been viewed.
   *
   * @return array
   *   An array of value objects representing the number of times each entity
   *   has been viewed. The array is keyed by entity ID. If an ID does not
   *   exist, it will not be present in the array.
   */
  public function fetchViews(array $keys);

  /**
   * Delete counts for a list entity.
   *
   * @param array $ids
   *   The ID of the entity which views to delete.
   *
   * @return bool
   *   TRUE if the entity views have been deleted.
   */
  public function deleteRecords(array $ids);

  /**
   * Sync data from redis to database.
   *
   * @param array $counter
   *   The array is keyed by entity ID and value number of view.
   *
   * @return bool
   *   TRUE if the data have been sync.
   */
  public function syncDatabase(array $counter);

}
