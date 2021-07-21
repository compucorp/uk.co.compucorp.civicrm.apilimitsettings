<?php

/**
 * Tests the listener that changes the API settings.
 *
 * @group headless
 */
class CRM_Apilimitsettings_Event_Listener_ChangeApiSettingTest extends BaseHeadlessTest {

  /**
   * Tests the new default limit is set.
   */
  public function testNewDefaultApiLimitIsSet(): void {
    $expectedContacts = 30 + civicrm_api3('Contact', 'getcount');
    $this->createTestContacts(30);

    $contacts = civicrm_api3('Contact', 'get');

    $this->assertEquals($expectedContacts, $contacts['count']);
    $this->assertCount($expectedContacts, $contacts['values']);
  }

  /**
   * Tests specified value on query is respected.
   */
  public function testLimitValueOnQueryIsRespected(): void {
    $expectedContacts = 30 + civicrm_api3('Contact', 'getcount');
    $this->createTestContacts(30);

    $contacts = civicrm_api3('Contact', 'get', [
      'options' => ['limit' => 10],
    ]);
    $this->assertEquals(10, $contacts['count']);
    $this->assertCount(10, $contacts['values']);

    $totalContacts = civicrm_api3('Contact', 'getcount');
    $this->assertEquals($expectedContacts, $totalContacts);
  }

  /**
   * Tests limit value is respected, when multiple entities are involved.
   *
   * This proofs that one call does not affect the other.
   */
  public function testLimitValueOnQueryIsRespectedOnDifferentEntities(): void {
    $this->createTestContacts(30);
    $expectedTags = 35 + civicrm_api3('Tag', 'getcount');
    $this->createTestTags(35);

    $contacts = civicrm_api3('Contact', 'get', [
      'options' => ['limit' => 10],
    ]);
    $this->assertEquals(10, $contacts['count']);
    $this->assertCount(10, $contacts['values']);

    $tags = civicrm_api3('Tag', 'get');
    $this->assertEquals($expectedTags, $tags['count']);
    $this->assertCount($expectedTags, $tags['values']);
  }

  /**
   * Create test Contacts.
   *
   * @param int $instanceNumber
   *   Number of instances to be created.
   */
  private function createTestContacts(int $instanceNumber): void {
    for ($i = 0; $i < $instanceNumber; $i++) {
      $rand = random_int(10000, 99999);
      civicrm_api3('Contact', 'create', [
        'contact_type' => 'Individual',
        'first_name' => 'Test FN ' . $rand,
        'last_name' => 'Test LN ' . $rand,
        'email' => "test{$rand}@example.com",
      ]);
    }
  }

  /**
   * Create test Tags.
   *
   * @param int $instanceNumber
   *   Number of instances to be created.
   */
  private function createTestTags(int $instanceNumber): void {
    for ($i = 0; $i < $instanceNumber; $i++) {
      $rand = rand(10000, 99999);
      civicrm_api3('Tag', 'create', [
        'name' => 'Tag Name ' . $rand,
      ]);
    }
  }

}
