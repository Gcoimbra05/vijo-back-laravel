<?php

namespace App\Docs;

use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Vijo",
 *      description="Documentation for the Vijo system API",
 * )
 *
 * @OA\Post(
 *     path="/v2/resend_2fa",
 *     summary="Resend 2FA authentication code to user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"otp_id"},
 *                 @OA\Property(
 *                     property="otp_id",
 *                     type="integer",
 *                     description="ID of the OTP verification record"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="A new code has been resent to your email and/or mobile.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="otp_id", type="integer"),
 *                 @OA\Property(property="expires_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Session expired. Please log in again.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Authentication request not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User not authorized.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/send-reset-link",
 *     summary="Send password reset link to the provided email",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reset link sent to your email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Reset link sent to your email.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/password/reset",
 *     summary="Reset password and log in",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "token", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="token", type="string", example="generated-token"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login successful."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="refresh_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token or email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid token or email.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/sign-up",
 *     summary="Register a new user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "email", "password", "confirm_password"},
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="password123"),
 *             @OA\Property(property="country_code", type="string", example="+1"),
 *             @OA\Property(property="mobile", type="string", example="5551234567"),
 *             @OA\Property(property="optInNewsUpdates", type="boolean", example=true),
 *             @OA\Property(property="timezone", type="string", example="America/New_York")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User registered successfully."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="otp_id", type="integer"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="A user with this email already exists.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="A user with this email already exists.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Password and confirm password do not match.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Password and confirm password do not match.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to send OTP.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to send OTP.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/logout",
 *     summary="Logout authenticated user",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logged out successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logged out successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/forgot-password",
 *     summary="Request password reset link",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset link sent to your email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password reset link sent to your email.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Unable to send reset link. Please check the email address.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unable to send reset link. Please check the email address.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/reset-password",
 *     summary="Reset user password",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "token", "password", "password_confirmation"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="token", type="string", example="generated-token"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password has been reset successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password has been reset successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token or email.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid token or email.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/verify-email",
 *     summary="Verify user email",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "hash"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="hash", type="string", example="sha1-of-email")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email verified successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Email verified successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid verification link.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid verification link.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/verify-email-resend",
 *     summary="Resend verification email to user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verification email resent.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Verification email resent.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email already verified.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Email already verified.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/sign-in",
 *     summary="Send 2FA code for user login (email or phone)",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"type"},
 *             @OA\Property(property="type", type="string", enum={"email", "phone"}, description="Type of login: email or phone"),
 *             @OA\Property(property="email", type="string", format="email", description="User email (required if type is email)"),
 *             @OA\Property(property="mobile", type="string", description="User mobile number (required if type is phone)"),
 *             @OA\Property(property="country_code", type="string", description="Country code (required if type is phone)"),
 *             @OA\Property(property="password", type="string", description="User password (optional)"),
 *             @OA\Property(property="trust_device", type="boolean", description="Trust this device for future logins"),
 *             @OA\Property(property="device_token", type="string", description="Device token for trusted device"),
 *             @OA\Property(property="device_name", type="string", description="Device name for trusted device")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Code sent or login successful (trusted device)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="otp_id", type="integer", nullable=true),
 *                 @OA\Property(property="skip_code", type="boolean"),
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="refresh_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean"),
 *                 @OA\Property(property="last_4_digits", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No account exists with the provided details.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid password.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/validate_2fa",
 *     summary="Validate 2FA code and log in user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"otp", "otp_id"},
 *             @OA\Property(property="otp", type="string", description="The 2FA code sent to the user"),
 *             @OA\Property(property="otp_id", type="integer", description="ID of the OTP verification record")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object"),
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="refresh_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer"),
 *                 @OA\Property(property="loggedIn", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid, expired, or already used code.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/refresh-token",
 *     summary="Refresh the user's access token using a valid refresh token",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Token refreshed successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="access_token", type="string"),
 *                 @OA\Property(property="expires_in", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Token still valid. No new token generated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string"),
 *                     @OA\Property(property="expires_in", type="integer")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized or invalid/expired token.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/auth/validate-token",
 *     summary="Validate the user's access token",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"access_token"},
 *             @OA\Property(property="access_token", type="string", description="Access token to validate")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Token is valid.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="user_id", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized or invalid/expired token.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="statusCode", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="detail", type="object",
 *                     @OA\Property(property="message", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/countries",
 *     summary="Get list of supported countries with phone codes",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="List of countries returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="code", type="integer", example=1),
 *                     @OA\Property(property="label", type="string", example="US (+1)")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/onboarding-contents",
 *     summary="Get onboarding content for user registration and sign-in flows",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="Onboarding content returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="onboardingContents", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="pageSlug", type="string", example="home"),
 *                         @OA\Property(property="pageName", type="string", example="Home"),
 *                         @OA\Property(property="title", type="string", example="Your Life today is May 16"),
 *                         @OA\Property(property="subtitle", type="string", example="Let's VIJO!"),
 *                         @OA\Property(property="description", type="string", example="Capture life's precious moments with VIJO—your personal time capsule for memories, emotions, and growth."),
 *                         @OA\Property(property="message", type="string", example="")
 *                     )
 *                 ),
 *                 @OA\Property(property="onboardingEmoji", type="string", example="1f3a5")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/information-contents",
 *     summary="Get informational content for emotional outcomes and insights",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="Information content returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="informationContents", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="pageSlug", type="string", example="emotional_outcome"),
 *                         @OA\Property(property="pageName", type="string", example="Emotional Outcome"),
 *                         @OA\Property(property="title", type="string", example="Emotional Outcome"),
 *                         @OA\Property(property="description", type="string", example="Emotional intelligence (EQ) is the ability to understand, interpret, and control emotions to better communicate and relate to others constructively.")
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/static-pages",
 *     summary="Get static pages content such as FAQs and privacy policy",
 *     tags={"Settings"},
 *     @OA\Response(
 *         response=200,
 *         description="Static pages content returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="faqs", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="title", type="string", example="Why Video Journaling?"),
 *                         @OA\Property(property="contents", type="string", example="Video Journaling is a safe, secure and easy way for people to reflect on meaningful experiences to document their life.")
 *                     )
 *                 ),
 *                 @OA\Property(property="privacy_policy", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="name", type="string", example="Privacy Policy 1.0")
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/shared-video-details/{id}",
 *     summary="Get details of a shared video journal by ID",
 *     tags={"Video Journal"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the shared video journal",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Shared journal details returned successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="journal_data", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=123),
 *                         @OA\Property(property="journal_title", type="string", example="My Journal Title"),
 *                         @OA\Property(property="category_name", type="string", example="Work"),
 *                         @OA\Property(property="journal_tags", type="string", example="1,2,3"),
 *                         @OA\Property(property="is_private", type="integer", example=0),
 *                         @OA\Property(property="video", type="string", example="https://example.com/video.mp4"),
 *                         @OA\Property(property="video_thumb", type="string", example="https://example.com/thumb.jpg"),
 *                         @OA\Property(property="user_tags", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"))),
 *                         @OA\Property(property="emotions", type="object"),
 *                         @OA\Property(property="transcription", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="answer", type="string"), @OA\Property(property="thumb", type="string"), @OA\Property(property="emoji", type="string"), @OA\Property(property="emotion_score", type="number"), @OA\Property(property="text", type="string"), @OA\Property(property="emotion", type="string"))),
 *                         @OA\Property(property="final_video_transcript", type="string"),
 *                         @OA\Property(property="summaryReport", type="string"),
 *                         @OA\Property(property="video_type_id", type="string"),
 *                         @OA\Property(property="catalog_id", type="string"),
 *                         @OA\Property(property="catalog_name", type="string"),
 *                         @OA\Property(property="created_at", type="string", example="Sep 23, 2025"),
 *                         @OA\Property(property="contact", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="email", type="string"), @OA\Property(property="mobile", type="string")),
 *                         @OA\Property(property="group", type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string"))
 *                     )
 *                 ),
 *                 @OA\Property(property="contacts", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="first_name", type="string"), @OA\Property(property="last_name", type="string"), @OA\Property(property="email", type="string"), @OA\Property(property="mobile", type="string"))),
 *                 @OA\Property(property="groups", type="array", @OA\Items(@OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string")))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Id parameter is required.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Id parameter is required"),
 *             @OA\Property(property="results", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Journal not found or access denied.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Journal not found or access denied."),
 *             @OA\Property(property="results", type="null")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/response-request-details/{sharedId}",
 *     summary="Get details for a shared video request",
 *     description="Returns detailed information about a video request using a shared (base64) ID. Includes catalog info, record times, video type metrics, and related questions.",
 *     tags={"Video Requests"},
 *     @OA\Parameter(
 *         name="sharedId",
 *         in="path",
 *         required=true,
 *         description="Base64 encoded ID of the shared video request",
 *         @OA\Schema(type="string", example="MTAwMQ==")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Video request details retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="parent_catalog_id", type="string", example="0"),
 *                 @OA\Property(property="catalog_id", type="string", example="1001"),
 *                 @OA\Property(property="request_id", type="integer", example=1234),
 *                 @OA\Property(property="record_date", type="string", format="date", example="2025-10-07"),
 *                 @OA\Property(property="video_types", type="object",
 *                     @OA\Property(property="metrics", type="integer", example=6),
 *                     @OA\Property(property="kpis", type="integer", example=2),
 *                     @OA\Property(property="kpi_metrics", type="integer", example=3)
 *                 ),
 *                 @OA\Property(property="video_type_id", type="integer", example=1),
 *                 @OA\Property(property="min_record_time", type="string", example="15"),
 *                 @OA\Property(property="record_time", type="string", example="60"),
 *                 @OA\Property(
 *                     property="questions",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Metric Name"),
 *                         @OA\Property(property="question", type="string", example="What is your score?"),
 *                         @OA\Property(property="video_question", type="string", example="Describe your experience."),
 *                         @OA\Property(property="range", type="string", example="1-10")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or missing sharedId parameter.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Id parameter is required"),
 *             @OA\Property(property="results", type="object", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized access to this request.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized access to this request.")
 *         )
 *     )
 * )
 *
 *  * @OA\Get(
 *     path="/v2/users",
 *     summary="List all users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="last_name", type="string", example="Smith"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="country_code", type="string", example="+1"),
 *                 @OA\Property(property="mobile", type="string", example="5551234567")
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/v2/users/{id}",
 *     summary="Get user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
*     @OA\Response(
*         response=200,
*         description="User found",
*         @OA\JsonContent(
*             @OA\Property(property="id", type="integer", example=1),
*             @OA\Property(property="first_name", type="string", example="John"),
*             @OA\Property(property="last_name", type="string", example="Smith"),
*             @OA\Property(property="email", type="string", example="john@example.com"),
*             @OA\Property(property="country_code", type="string", example="+1"),
*             @OA\Property(property="mobile", type="string", example="5551234567")
*         )
*     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
*
 * @OA\Post(
 *     path="/v2/users",
 *     summary="Create a new user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "email", "password", "confirm_password"},
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="password123"),
 *             @OA\Property(property="country_code", type="string", example="+1"),
 *             @OA\Property(property="mobile", type="string", example="5551234567"),
 *             @OA\Property(property="optInNewsUpdates", type="boolean", example=true),
 *             @OA\Property(property="timezone", type="string", example="America/New_York")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User registered successfully."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="userData", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="first_name", type="string", example="John"),
 *                     @OA\Property(property="last_name", type="string", example="Smith"),
 *                     @OA\Property(property="email", type="string", example="john@example.com"),
 *                     @OA\Property(property="country_code", type="string", example="+1"),
 *                     @OA\Property(property="mobile", type="string", example="5551234567")
 *                 ),
 *                 @OA\Property(property="otp_id", type="integer", example=12345),
 *                 @OA\Property(property="expires_in", type="integer", example=3600),
 *                 @OA\Property(property="loggedIn", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="A user with this email or phone already exists.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="A user with this email already exists.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Password and confirm password do not match.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Password and confirm password do not match.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to send OTP.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to send OTP.")
 *         )
 *     )
 * ) 
*
 * @OA\Put(
 *     path="/v2/users/{id}",
 *     summary="Update user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Smith"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
 *             @OA\Property(property="country_code", type="string", example="+1"),
 *             @OA\Property(property="mobile", type="string", example="5551234567"),
 *             @OA\Property(property="reminders", type="boolean", example=true),
 *             @OA\Property(property="notifications", type="boolean", example=true),
 *             @OA\Property(property="timezone", type="string", example="America/New_York"),
 *             @OA\Property(property="optInNewsUpdates", type="boolean", example=true),
 *             @OA\Property(property="two_factor_enabled", type="boolean", example=true)
 *         )
 *     ),
*     @OA\Response(
*         response=200,
*         description="User updated successfully.",
*         @OA\JsonContent(
*             @OA\Property(property="id", type="integer", example=1),
*             @OA\Property(property="first_name", type="string", example="John"),
*             @OA\Property(property="last_name", type="string", example="Smith"),
*             @OA\Property(property="email", type="string", example="john@example.com"),
*             @OA\Property(property="country_code", type="string", example="+1"),
*             @OA\Property(property="mobile", type="string", example="5551234567")
*         )
*     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/v2/users/{id}",
 *     summary="Delete user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/user/feedback",
 *     summary="Submit user feedback",
 *     description="Allows an authenticated user to submit feedback. Sends an email notification to support and stores the feedback in the database.",
 *     tags={"User Feedback"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"type", "message"},
 *             @OA\Property(property="type", type="string", example="bug", description="Type of feedback (e.g. bug, suggestion, other)"),
 *             @OA\Property(property="message", type="string", example="I found a bug in the dashboard.", description="Feedback message"),
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User email (optional)"),
 *             @OA\Property(property="subject", type="string", example="Dashboard Bug", description="Feedback subject (optional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Feedback submitted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Feedback submitted successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=123),
 *                 @OA\Property(property="type", type="string", example="bug"),
 *                 @OA\Property(property="message", type="string", example="I found a bug in the dashboard."),
 *                 @OA\Property(property="email", type="string", example="user@example.com"),
 *                 @OA\Property(property="subject", type="string", example="Dashboard Bug"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-07T12:34:56Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="type", type="array", @OA\Items(type="string", example="The type field is required.")),
 *                 @OA\Property(property="message", type="array", @OA\Items(type="string", example="The message field is required."))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/v2/user/delete-account",
 *     summary="Permanently delete the authenticated user's account and all related data",
 *     description="Deletes the authenticated user's account and all associated records (videos, feedback, contacts, etc). This action is irreversible.",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Account and all user data deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Account and all user data deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error deleting account.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Error deleting account: [error details]")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/dashboard",
 *     summary="Get dashboard data for the authenticated user",
 *     description="Returns a comprehensive set of dashboard data for the authenticated user, including activity, categories, daily messages, guided tours, membership plans, journals, quick goals, and more.",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Dashboard data retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="activity", type="object"),
 *                 @OA\Property(property="categories", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="coming_soon", type="object"),
 *                 @OA\Property(property="current_date", type="string", example="2025-10-07"),
 *                 @OA\Property(property="currentDate", type="string", example="Tuesday, October 7, 2025"),
 *                 @OA\Property(property="daily_message", type="object"),
 *                 @OA\Property(property="filterByLabels", type="object"),
 *                 @OA\Property(property="graphTypes", type="object"),
 *                 @OA\Property(property="greeting", type="string", example="Good morning, John!"),
 *                 @OA\Property(property="guidedTours", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="guidedToursTaken", type="integer", example=1),
 *                 @OA\Property(property="insightFilters", type="object"),
 *                 @OA\Property(property="membershipPlan", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="myJournals", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="plans", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="promotionalCatalogs", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="quick_goals", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="rangeTypeLabels", type="object"),
 *                 @OA\Property(property="responceCount", type="object"),
 *                 @OA\Property(property="timezoneMenus", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="userPlan", type="object"),
 *                 @OA\Property(property="userTags", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="vijo_of_day", type="object"),
 *                 @OA\Property(property="viewByLabels", type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated.")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/emotional-snapshot",
 *     summary="Get emotional snapshot for the authenticated user",
 *     description="Returns the latest emotional snapshot for the authenticated user, including the last Vijo (journal) title and date.",
 *     tags={"Emlo"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Emotional snapshot retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Emotional snapshot retrieved successfully."),
 *             @OA\Property(property="data", type="object", description="Snapshot data (structure depends on implementation)"),
 *             @OA\Property(property="last_vijo", type="object",
 *                 @OA\Property(property="title", type="string", example="My Journal Title"),
 *                 @OA\Property(property="date", type="string", example="Oct 07, 2025")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/v2/update-guided-tours",
 *     summary="Update guided tours status for the authenticated user",
 *     description="Updates the guided tours status (taken or not) for the authenticated user.",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"guided_tours"},
 *             @OA\Property(property="guided_tours", type="integer", enum={0,1}, example=1, description="Guided tours status: 0 (not taken), 1 (taken)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Guided tour updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Guided tour updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="guided_tours", type="array", @OA\Items(type="string", example="The guided_tours field is required."))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/subscription-plans",
 *     summary="Get available subscription plans and the user's current plan",
 *     tags={"User"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of available subscription plans and user's current plan",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="plans", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="1"),
 *                         @OA\Property(property="slug", type="string", example="basic"),
 *                         @OA\Property(property="title", type="string", example="Basic Plan"),
 *                         @OA\Property(property="description", type="string", example="Access to basic features"),
 *                         @OA\Property(property="paymentLink", type="string", example="https://payment.example.com/plan/basic")
 *                     )
 *                 ),
 *                 @OA\Property(property="userPlan", type="object",
 *                     @OA\Property(property="id", type="string", example="2"),
 *                     @OA\Property(property="slug", type="string", example="premium"),
 *                     @OA\Property(property="title", type="string", example="Premium Plan"),
 *                     @OA\Property(property="description", type="string", example="Access to all premium features")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/membership-plans",
 *     summary="Get all available membership plans",
 *     tags={"Membership Plans"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of available membership plans",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plans", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Basic"),
 *                         @OA\Property(property="description", type="string", example="Basic plan features"),
 *                         @OA\Property(property="slug", type="string", example="basic")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No membership plans available",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="No membership plans available."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plans", type="array", @OA\Items())
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/v2/membership-plans/{membership_plan}",
 *     summary="Get details of a specific membership plan",
 *     tags={"Membership Plans"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="membership_plan",
 *         in="path",
 *         required=true,
 *         description="ID of the membership plan",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Membership plan details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example=""),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plan", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Basic"),
 *                     @OA\Property(property="description", type="string", example="Basic plan features"),
 *                     @OA\Property(property="slug", type="string", example="basic")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Membership plan not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Membership plan not found."),
 *             @OA\Property(property="results", type="object",
 *                 @OA\Property(property="membership_plan", type="null")
 *             )
 *         )
 *     )
 * )
*/

class SwaggerAnnotations extends Controller
{
}