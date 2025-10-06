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
 */

class SwaggerAnnotations extends Controller
{
}