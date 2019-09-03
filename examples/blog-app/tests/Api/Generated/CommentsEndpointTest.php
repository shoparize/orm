<?php

namespace Example\BlogApp\Test\Api\Generated;

use Example\BlogApp\TableGateways;
use Example\BlogApp\Models\CommentsModel;
use Example\BlogApp\Services\CommentsService;

class CommentsEndpointTest extends \Gone\AppCore\Test\RoutesTestCase
{

    const MODEL_NAME = 'Comments';

    public function testCommentsCreate()
    {
        $this->waypoint("Begin");
        /** @var CommentsService $service */
        $service = $this->getApp()->getContainer()->get(CommentsService::class);
        /** @var CommentsModel $newComments **/
        $newComments = $service->getMockObject();

        $this->waypoint("Initialise Mock Model");
        $newCommentsArray = $newComments->__toArray();

        $method = "PUT";
        $uri    = "/v1/comments";

        $response = $this->request(
            $method,
            $uri,
            $newCommentsArray
        );
        $this->waypoint("API PUT REST REQUEST");
        $body = (string) $response->getBody();

        ob_start();
        \Kint::dump(
            $newCommentsArray,
            json_encode($newCommentsArray, JSON_PRETTY_PRINT)
        );
        $requestCommentsParams = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(
            in_array(
                $response->getStatusCode(),
                [
                    200,
                    400,
                ]
            ),
            "Response was not expected 200 or 400.\n" .
            "Request: {$method} => {$uri}" .
            "{$requestCommentsParams}\n" .
            "Response body is: \n" .
            " ******************************\n{$body}\n ******************************\n"
        );

        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\" Request JSON blob: \"" . json_encode($newComments->__toArray()) . "\""
        );
        $responseJson = json_decode($body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to PUT /v1/comments returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Comments', $responseJson);
        $this->waypoint("Some assertions");

        #\Kint::dump(
        #    $newCommentsArray,
        #    $responseJson
        #);

        $this->validateCommentsObject($responseJson['Comments']);
        $this->waypoint("Validate Object Response");
        //TODO: Make this respect primary key field instead of assuming ID.
        if(!isset($responseJson['Comments']['Id'])){
            $this->markTestIncomplete("Skipped test... Comments response object doesn't have an ID field.");
        }

        return $responseJson['Comments']['Id'];
    }


    public function testCommentsCreateFails()
    {
        $this->waypoint("Begin");

        $newComments = [
            'id' => null,
            'comment' => null,
            'authorId' => null,
            'publishedDate' => null,
        ];
        $response = $this->request("PUT", "/v1/comments", $newComments);
        $this->waypoint("API PUT REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\" Request JSON blob: \"" . json_encode($newComments) . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Fail", $responseJson['Status'], "Object was created, when failure was expected.");
        $this->waypoint("Some assertions");
    }

    /**
     * @depends testCommentsCreate
     */
    public function testCommentsGet($id)
    {
        $this->waypoint("Begin");
        $response = $this->request("GET", "/v1/comments/{$id}");
        $this->waypoint("API GET REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to GET /v1/comments/{$id} returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Comments', $responseJson);
        $this->waypoint("Some assertions");

        $this->validateCommentsObject($responseJson['Comments']);
        $this->waypoint("Validate Object Response");
    }

    /**
     * @depends testCommentsCreate
     */
    public function testCommentsList()
    {
        $this->waypoint("Begin");
        $response = $this->request("GET", "/v1/comments");
        $this->waypoint("API REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to GET /v1/comments returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Comments', $responseJson);
        $this->waypoint("Some assertions");
        $this->validateCommentsObject(reset($responseJson['Comments']));
        $this->waypoint("Validate Object Response");

    }

    /**
     * @depends testCommentsCreate
     */
    public function testCommentsDelete($id)
    {
        $response = $this->request("DELETE", "/v1/comments/{$id}");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to DELETE /v1/comments/{$id} returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertEquals("DELETE", $responseJson['Action']);
        $this->assertArrayHasKey('Comments', $responseJson);
        $this->validateCommentsObject($responseJson['Comments']);
        return $id;
    }

    /**
     * @depends testCommentsDelete
     */
    public function testCommentsDeleteVerify($id)
    {
        $response = $this->request("GET", "/v1/comments/{$id}");
        $body = (string) $response->getBody();
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->assertEquals("Fail", $responseJson['Status']);
    }

    private function validateCommentsObject($CommentsObject)
    {
        $this->assertArrayHasKey('Id', $CommentsObject, "There was no element with the key 'id'.");
        $this->assertArrayHasKey('Comment', $CommentsObject, "There was no element with the key 'comment'.");
        $this->assertArrayHasKey('AuthorId', $CommentsObject, "There was no element with the key 'authorId'.");
        $this->assertArrayHasKey('PublishedDate', $CommentsObject, "There was no element with the key 'publishedDate'.");
    }
}
