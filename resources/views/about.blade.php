@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

<!-- About Section -->
<div class="container">
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
            <li>✅ <strong>Identifies blind spots and anti-patterns</strong> before they become blockers.</li>
            <li>✅ <strong>Delivers AI-powered recommendations</strong> tailored to team challenges.</li>
            <li>✅ <strong>Provides leadership with actionable insights</strong> to foster true Agile culture.</li>
            <li>✅ <strong>Creates a measurable path forward</strong>, turning Agile adoption into Agile mastery.</li>
        </ul>
    </div>

    <div class="content-section">
        <h5 class="content-title">Features</h5>
        <ul>
            <li>Team Creation & Management: Easily create and manage teams for assessments.</li>
            <li>Agile Maturity Assessment: Fill out assessments to evaluate team maturity across different Agile categories.</li>
            <li>Customizable Question Library: Manage, add, and update questions in the assessment library.</li>
            <li>Assessment Reports: Download detailed reports, including radar charts and feedback, to help visualize and analyze team maturity.</li>
            <li>Insights & Metrics: View category averages, overall maturity scores, and comparison with past assessments or industry benchmarks.</li>
            <li>Anti-Patterns & Recommendations: Detect Agile anti-patterns and receive actionable insights and recommended Agile practices.</li>
            <li>User Dashboard: Users can view and manage their teams, personal assessment results, and track their progress over time.</li>
        </ul>
    </div>

    <div class="content-section">
        <h5 class="content-title">Project Status</h5>
        <p><strong>Current Version:</strong> 1.0.0-alpha</p>
        <p><strong>Milestones:</strong></p>
        <ul>
            <li>Version 1.0.0-beta: Beta release</li>
            <li>Future enhancements: Continuous improvements to features, question libraries, and additional insights and AI assistive technology.</li>
        </ul>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
