@extends('layouts.home')

@section('content')
    <style>
        /* General Styles for Full-Width Layout */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .full-width-container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Hero Block Styles */
        .hero-section {
            position: relative;
            width: 100%;
            height: 30vh; /* Adjust height as needed */
            background: url('{{ asset('imgs/about.jpeg') }}') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Overlay for better text visibility */
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2; /* Ensure it sits above the overlay */
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .hero-content p {
            font-size: 1.25rem;
        }

        /* Content Section Styles */
        .content-section-container {
            width: 100%;
            padding: 2rem 10%; /* Wide spacing around the content; adjust as needed */
            background-color: #f8f9fa;
        }

        .content-section {
            margin-bottom: 2rem;
        }

        .about-heading {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: bold;
        }

        .content-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.75rem;
        }

        .content-section p {
            font-size: 1rem;
            line-height: 1.6;
        }

        .content-section ul {
            margin-top: 1rem;
            padding-left: 1.5rem;
            list-style-type: none;
        }

        .content-section ul li {
            font-size: 1rem;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .content-section ul li::before {
            content: '✅';
            position: absolute;
            left: -1.5rem;
            color: #28a745;
        }
    </style>

    <!-- Hero Section -->
    <section class="hero-section full-width-container">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to Our About Page</h1>
            <p>Learn more about us and our mission.</p>
        </div>
    </section>

    <!-- Full-Width Content -->
    <div class="content-section-container">
        <h1 class="about-heading">About Radigile</h1>

        <div class="content-section">
            <h5 class="content-title">Product Vision</h5>
            <p>To empower teams with the insights, tools, and guidance to master Agile, break through plateaus, and achieve lasting transformation.</p>
        </div>

        <div class="content-section">
            <h5 class="content-title">Mission Statement</h5>
            <p>Our mission is to empower teams to unlock their full potential by providing tailored assessments, actionable insights, and continuous learning opportunities. We are committed to guiding organizations through their Agile transformation journey, enabling them to build high-performing teams that deliver exceptional value and drive sustainable change.</p>
        </div>

        <div class="content-section">
            <h5 class="content-title">Why This Matters</h5>
            <p>Agile teams today are stuck in a cycle of <em>doing Agile</em> rather than <em>being Agile</em>. They follow the motions—stand-ups, retros, velocity tracking—without truly evolving. Leaders struggle to measure progress, teams hit invisible barriers, and organizations invest in Agile without understanding if it’s actually working.</p>
            <p><strong>The result?</strong> Stagnation, frustration, and missed opportunities for real growth.</p>
        </div>

        <div class="content-section">
            <h5 class="content-title">The Solution: Data-Driven Growth with Personalized Insights</h5>
            <p>We believe Agile is more than just frameworks—it’s a culture, a mindset, and a relentless pursuit of excellence. Our platform provides a dynamic <strong>Agile Maturity Assessment Tool</strong> that goes beyond checklists and self-reported scores. It:</p>
            <ul>
                <li><strong>Identifies blind spots and anti-patterns</strong> before they become blockers.</li>
                <li><strong>Delivers AI-powered recommendations</strong> for targeted improvements.</li>
                <li><strong>Offers continuous feedback</strong> and resources tailored to your team’s needs.</li>
            </ul>
        </div>
    </div>
@endsection
