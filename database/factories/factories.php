<?php

namespace Database\Factories;

use App\Models\Affiliate;
use App\Models\Catalog;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\EmloResponse;
use App\Models\EmloResponsePath;
use App\Models\EmloResponseSegment;
use App\Models\EmloResponseValue;
use App\Models\MembershipPlan;
use App\Models\ReferralCode;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\Video;
use App\Models\VideoRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliateFactory extends Factory
{
    protected $model = Affiliate::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'status' => $this->faker->word,
        ];
    }
}

class CatalogFactory extends Factory
{
    protected $model = Catalog::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'tags' => $this->faker->words(3, true),
            'min_record_time' => $this->faker->numberBetween(1, 10),
            'max_record_time' => $this->faker->numberBetween(11, 30),
            'emoji' => $this->faker->emoji,
            'is_deleted' => $this->faker->boolean,
            'status' => $this->faker->numberBetween(0, 3),
        ];
    }
}

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'group_id' => ContactGroup::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'country_code' => $this->faker->countryCode,
            'mobile' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'status' => $this->faker->numberBetween(0, 2),
        ];
    }
}

class ContactGroupFactory extends Factory
{
    protected $model = ContactGroup::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word,
            'status' => $this->faker->numberBetween(0, 2),
        ];
    }
}

class EmloResponseFactory extends Factory
{
    protected $model = EmloResponse::class;

    public function definition()
    {
        return [
            'request_id' => VideoRequest::factory(),
            'raw_response' => json_encode($this->faker->paragraph),
        ];
    }
}

class EmloResponsePathFactory extends Factory
{
    protected $model = EmloResponsePath::class;

    public function definition()
    {
        return [
            'path_key' => $this->faker->word,
            'json_path' => $this->faker->sentence,
            'data_type' => $this->faker->word,
        ];
    }
}

class EmloResponseSegmentFactory extends Factory
{
    protected $model = EmloResponseSegment::class;

    public function definition()
    {
        return [
            'number' => $this->faker->numberBetween(1, 10),
            'name' => $this->faker->word,
        ];
    }
}

class EmloResponseValueFactory extends Factory
{
    protected $model = EmloResponseValue::class;

    public function definition()
    {
        return [
            'response_id' => EmloResponse::factory(),
            'path_id' => EmloResponsePath::factory(),
            'value' => $this->faker->text,
        ];
    }
}

class MembershipPlanFactory extends Factory
{
    protected $model = MembershipPlan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'payment_mode' => $this->faker->numberBetween(1, 2),
            'monthly_cost' => $this->faker->randomFloat(2, 0, 100),
            'annual_cost' => $this->faker->randomFloat(2, 0, 1000),
            'payment_link' => $this->faker->url,
            'status' => $this->faker->numberBetween(0, 2),
        ];
    }
}

class ReferralCodeFactory extends Factory
{
    protected $model = ReferralCode::class;

    public function definition()
    {
        return [
            'affiliate_id' => Affiliate::factory(),
            'code' => $this->faker->unique()->word,
            'commission' => $this->faker->randomFloat(2, 0, 100),
            'number_uses' => $this->faker->numberBetween(0, 100),
            'max_number_uses' => $this->faker->numberBetween(1, 100),
            'discount' => $this->faker->randomFloat(2, 0, 100),
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
        ];
    }
}

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => MembershipPlan::factory(),
            'stripe_customer_id' => $this->faker->word,
            'stripe_subscription_id' => $this->faker->word,
            'status' => $this->faker->numberBetween(1, 5),
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
            'cancel_at' => $this->faker->dateTime,
            'cancelled_at' => $this->faker->dateTime,
            'reason' => $this->faker->sentence,
        ];
    }
}

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'country_code' => $this->faker->countryCode,
            'mobile' => $this->faker->phoneNumber,
            'guided_tours' => $this->faker->boolean,
            'last_login_date' => $this->faker->dateTime,
            'status' => $this->faker->numberBetween(0, 3),
            'is_verified' => $this->faker->boolean,
            'plan_id' => MembershipPlan::factory(),
        ];
    }
}

class UserVerificationFactory extends Factory
{
    protected $model = UserVerification::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'code' => $this->faker->word,
            'expires_at' => $this->faker->dateTime,
            'is_used' => $this->faker->boolean,
        ];
    }
}

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition()
    {
        return [
            'request_id' => VideoRequest::factory(),
            'video_name' => $this->faker->word,
            'video_url' => $this->faker->url,
            'video_duration' => $this->faker->numberBetween(1, 300),
            'thumbnail_name' => $this->faker->word,
            'thumbnail_url' => $this->faker->url,
        ];
    }
}

class VideoRequestFactory extends Factory
{
    protected $model = VideoRequest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'catalog_id' => Catalog::factory(),
            'ref_user_id' => User::factory(),
            'ref_first_name' => $this->faker->firstName,
            'ref_last_name' => $this->faker->lastName,
            'ref_country_code' => $this->faker->countryCode,
            'ref_mobile' => $this->faker->phoneNumber,
            'ref_email' => $this->faker->unique()->safeEmail,
            'status' => $this->faker->numberBetween(0, 3),
        ];
    }
}