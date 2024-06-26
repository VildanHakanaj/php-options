<?php

namespace VildanHakanaj\Test\Unit;

use PHPUnit\Framework\TestCase;
use VildanHakanaj\Options;

class OptionsTest extends TestCase
{
    protected Options $options;

    protected function setUp(): void
    {
        $this->options = new Options($this->data());
    }

    /** @test */
    public function it_can_create_options_from_static_constructor(){
        $options = Options::fromArray([
            "key1" => "value1",
            "key2" => "value2"
        ]);

        $this->assertInstanceOf(Options::class, $options);

        $this->assertSame([
            "key1" => "value1",
            "key2" => "value2"
        ], $options->all());
    }

    /** @test */
    public function it_can_get_the_options_array()
    {
        $this->assertSame([
            "key1" => "value1",
            "key2" => "value2",
            "key3" => "value3",
            "key4" => "value4",
        ], $this->options->all());

    }

    /** @test */
    public function it_can_only_set_key_if_not_present_in_array()
    {
        $instance = $this->options->addIfUnique("uniqueKey", "uniqueValue");
        $instance2 = $this->options->addIfUnique("key1", "alreadyExists");

        $this->assertInstanceOf(Options::class, $instance);
        $this->assertInstanceOf(Options::class, $instance2);

        $this->assertSame("uniqueValue", $this->options->get("uniqueKey"));
        $this->assertNotSame("alreadyExists", $this->options->get("key1"));
        $this->assertSame("value1", $this->options->get("key1"));
    }

    /** @test */
    public function it_can_get_all_the_keys()
    {
        $this->assertSame([
            "key1",
            "key2",
            "key3",
            "key4",
        ], $this->options->keys());
    }

    /** @test */
    public function it_can_get_all_the_values()
    {
        $result = $this->options->values();

        $this->assertSame([
            "value1",
            "value2",
            "value3",
            "value4",
        ], $result);
    }

    /** @test */
    public function it_can_get_a_item_by_key()
    {
        $this->assertSame("value1", $this->options->get("key1"));
    }

    /** @test */
    public function it_returns_null_if_key_not_found()
    {
        $this->assertNull($this->options->get("noKey"));
    }

    /** @test */
    public function it_check_if_the_options_has_the_key()
    {
        $this->assertTrue($this->options->has("key1"));
        $this->assertFalse($this->options->has("notFound"));
    }

    /** @test */
    public function it_can_get_value_using_magic_getters()
    {
        $this->assertSame("value1", $this->options->key1);
        $this->assertNull($this->options->notFound);
    }

    /** @test */
    public function it_can_set_value_using_magic_setter()
    {
        $this->options->magicKey = "magicValue";
        $this->assertSame("magicValue", $this->options->get("magicKey"));
    }

    /** @test */
    public function it_can_get_a_value_using_options_as_array()
    {
        $this->assertSame("value1", $this->options["key1"]);
    }

    /** @test */
    public function it_can_check_if_key_isset_as_an_array()
    {
        $this->assertTrue(isset($this->options["key1"]));
        $this->assertFalse(isset($this->options["notFound"]));
    }

    /** @test */
    public function it_can_check_set_key_value_as_array()
    {
        $this->options["newKey"] = "newValue";
        $this->assertSame("newValue", $this->options->get("newKey"));
    }

    /** @test */
    public function it_can_unset_key_from_options_as_array()
    {
        unset($this->options["key1"]);
        $this->assertNull($this->options->get("key1"));
    }

    /** @test */
    public function it_can_merge_an_array()
    {
        $options = $this->options->merge([
            "key2" => "override2",
            "key3" => "value3"
        ]);

        $this->assertInstanceOf(Options::class, $options);

        $this->assertSame([
            "key1" => "value1",
            "key2" => "override2",
            "key3" => "value3",
            "key4" => "value4",
        ], $this->options->all());
    }

    /** @test */
    public function it_can_merge_a_key_value()
    {
        $options = $this->options
            ->mergeKey("newKey", "newValue")
            ->mergeKey("key1", "overrideValue1");
        $this->assertInstanceOf(Options::class, $options);
        $results = $options->all();

        $this->assertSame([
            "key1" => "overrideValue1",
            "key2" => "value2",
            "key3" => "value3",
            "key4" => "value4",
            "newKey" => "newValue"
        ], $results);
    }

    /** @test */
    public function it_can_override_the_options_with_the_given_array()
    {

        $result = $this->options->override([
            "key" => "value"
        ])->all();

        $this->assertSame(["key" => "value"], $result);
    }

    /** @test */
    public function it_can_loop_over_options_in_foreach()
    {
        $options = new Options([
            "showRightRail" => true,
            "pageTitle" => "Page Title"
        ]);

        $result = [];

        foreach ($options as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertSame(["showRightRail" => true, "pageTitle" => "Page Title"], $result);

    }

    /** @test */
    public function it_can_convert_options_into_json()
    {
        $json = $this->options->toJson();
        $this->assertJson($json);
        $this->assertSame('{"key1":"value1","key2":"value2","key3":"value3","key4":"value4"}', $json);
    }

    /** @test */
    public function it_can_filter_options_array(){
        $options = Options::fromArray([
            "key1" => false,
            "key2" => true,
            "key3" => "value1",
            "key4" => 0,
            "key5" => 1
        ]);

        $this->assertSame([
            "key1" => false,
            "key4" => 0,
        ], $options->filter(fn($option) => !$option));

        $this->assertSame([
            "key2" => true,
            "key3" => "value1",
            "key5" => 1,
        ], $options->filter());
    }


    /** @test */
    public function it_can_determine_if_a_field_is_enabled(){
        $this->options->override([
            'featureA' => true,
            'featureB' => 'Yes',
            'featureC' => 'On',
            'featureD' => 1,
        ]);

        $this->assertTrue($this->options->isEnabled('featureA'));
        $this->assertTrue($this->options->isEnabled('featureB'));
        $this->assertTrue($this->options->isEnabled('featureC'));
        $this->assertTrue($this->options->isEnabled('featureD'));
    }

    /** @test */
    public function it_can_determine_if_a_field_is_disabled(){
        $this->options->override([
            'featureA' => false,
            'featureB' => 'No',
            'featureC' => 'Off',
            'featureD' => 0,
        ]);

        $this->assertFalse($this->options->isDisabled('featureA'));
        $this->assertFalse($this->options->isDisabled('featureB'));
        $this->assertFalse($this->options->isDisabled('featureC'));
        $this->assertFalse($this->options->isDisabled('featureD'));
    }


    /** @test */
    public function it_returns_the_default_for_is_enabled_and_disabled_for_non_existing_keys(){
        $this->assertTrue($this->options->isEnabled('non-existing-key', true));
        $this->assertFalse($this->options->isEnabled('non-existing-key'));

        $this->assertTrue($this->options->isDisabled('non-existing-key'));
        $this->assertFalse($this->options->isDisabled('non-existing-key', false));
    }


    /** @test */
    public function it_can_filter_options_by_key(){

        $this->options->merge([
            "key11" => "value11",
            "key123" => "value123"
        ]);

        $filteredOptions = $this->options->filterByKey(function($key){
            return strpos($key, 'key1') !== false;
        });

        $this->assertSame([
            "key1" => "value1",
            "key11" => "value11",
            "key123" => "value123"
        ], $filteredOptions);

    }

    protected function data(): array
    {
        return [
            "key1" => "value1",
            "key2" => "value2",
            "key3" => "value3",
            "key4" => "value4",
        ];
    }
}
