<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentEvaluator;
use App\Models\AssessmentExcludedMember;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\AssessmentResult;
use App\Models\Invite;
use App\Models\Plan;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\TeamDomain;
use App\Models\TeamFramework;
use App\Models\TeamInvitation;
use App\Models\TeamMemberRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Rich, historical demo dataset — teams with a monthly cadence of *closed*
 * assessments stretching back several months (with real trend trajectories,
 * not random noise) so trend/analytics views have something worth showing.
 *
 * Deliberately NOT part of the default DatabaseSeeder chain — run explicitly:
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Safe to re-run: everything is looked up/created idempotently by name, and
 * historical assessments are only generated for teams that don't already
 * have them (tagged via a "[Demo]" title prefix).
 */
class DemoDataSeeder extends Seeder
{
    private const HISTORY_MONTHS = 9;

    public function run(): void
    {
        $this->command?->info('Seeding question library...');
        $this->seedQuestionLibrary();

        $this->command?->info('Seeding demo users...');
        $users = $this->seedUsers();

        $this->command?->info('Seeding demo teams...');
        $teams = $this->seedTeams($users);

        $this->command?->info('Seeding team invitations...');
        $this->seedTeamInvitations($teams, $users);

        $this->command?->info('Seeding admin invite codes...');
        $this->seedAdminInvites($users);

        $this->command?->info('Seeding historical assessments (this is the slow part)...');
        foreach ($teams as $team) {
            $this->seedHistoricalAssessmentsForTeam($team, $users);
        }

        $this->command?->info('Seeding current draft/active assessments...');
        foreach ($teams as $team) {
            $this->seedCurrentAssessmentForTeam($team);
        }

        $this->command?->info('Done.');
    }

    // ------------------------------------------------------------------
    // Question library
    // ------------------------------------------------------------------

    private function seedQuestionLibrary(): void
    {
        $extraCategories = [
            'Code Quality & Technical Debt',
            'Cross-Team Dependencies',
            'Incident Response',
        ];

        foreach ($extraCategories as $name) {
            QuestionCategory::firstOrCreate(['name' => $name]);
        }

        // (category name) => [ [question, [tip_0..tip_4]], ... ]
        $bank = [
            'Daily Standup' => [
                ['How consistently does the team hold standup on time?', [
                    'Standups rarely happen or are constantly skipped.',
                    'Standups happen sometimes but often late or half-attended.',
                    'Standups happen most days but run long or lose focus.',
                    'Standups happen consistently, on time, and stay focused.',
                    'Standups are sharp, timeboxed, and every member comes prepared.',
                ]],
                ['How well does the team surface blockers during standup?', [
                    'Blockers go unmentioned until they cause a missed deadline.',
                    'Blockers are mentioned but rarely followed up on.',
                    'Blockers are raised and sometimes tracked to resolution.',
                    'Blockers are raised, owned, and tracked until resolved.',
                    'Blockers are raised immediately and the team swarms to unblock.',
                ]],
            ],
            'Sprint Planning' => [
                ['How accurately does the team estimate story points?', [
                    'Estimates are guesses with no shared reference.',
                    'Estimates vary wildly between team members.',
                    'Estimates are reasonably consistent but often wrong.',
                    'Estimates are usually within range of actual effort.',
                    'Estimates are reliable and continuously calibrated against velocity.',
                ]],
                ['How clear are acceptance criteria before work starts?', [
                    'Work starts with no defined acceptance criteria.',
                    'Acceptance criteria exist but are vague or incomplete.',
                    'Acceptance criteria are usually written but inconsistently detailed.',
                    'Acceptance criteria are clear and agreed before work starts.',
                    'Acceptance criteria are precise, testable, and reviewed by the whole team.',
                ]],
            ],
            'Retrospectives' => [
                ['How often do retro action items actually get done?', [
                    'Action items are written down and never revisited.',
                    'A few action items get done, most are forgotten.',
                    'About half of action items are completed by the next retro.',
                    'Most action items are completed and tracked.',
                    'Action items are owned, tracked, and consistently closed out.',
                ]],
                ['How psychologically safe does the team feel raising problems?', [
                    'People avoid raising problems for fear of blame.',
                    'Only a few vocal members raise concerns.',
                    'Most people speak up but some hesitate on sensitive topics.',
                    'The team openly raises problems without fear.',
                    'The team actively surfaces and welcomes hard conversations.',
                ]],
            ],
            'Backlog Grooming' => [
                ['How far ahead does the team groom the backlog?', [
                    'Backlog grooming happens reactively, mid-sprint.',
                    'Grooming happens just before planning, rushed.',
                    'The next sprint is usually groomed in advance.',
                    'Two sprints ahead are consistently groomed.',
                    'The backlog is continuously groomed multiple sprints ahead.',
                ]],
                ['How well are dependencies identified before work starts?', [
                    'Dependencies are discovered mid-implementation.',
                    'Dependencies are occasionally flagged, often too late.',
                    'Most dependencies are identified during grooming.',
                    'Dependencies are consistently mapped before commitment.',
                    'Dependencies are proactively tracked and coordinated cross-team.',
                ]],
            ],
            'Team Collaboration' => [
                ['How effectively does the team pair or mob on hard problems?', [
                    'Everyone works in isolation, even on hard problems.',
                    'Pairing happens rarely and only when forced.',
                    'Pairing happens occasionally on the hardest problems.',
                    'Pairing is a normal, regular part of how the team works.',
                    'The team fluidly pairs/mobs whenever it adds value, no friction.',
                ]],
                ['How well does the team share context across members?', [
                    'Knowledge is siloed in one or two people.',
                    'Some sharing happens but big gaps remain.',
                    'Most context is shared, with occasional silos.',
                    'Context is actively shared through docs and conversation.',
                    'The team has no single point of failure for knowledge.',
                ]],
            ],
            'Code Quality & Technical Debt' => [
                ['How consistently does the team address technical debt?', [
                    'Technical debt is never prioritized, only new features.',
                    'Technical debt is discussed but rarely scheduled.',
                    'Some technical debt work happens most sprints.',
                    'Technical debt is regularly planned and paid down.',
                    'Technical debt is tracked, visible, and proactively managed.',
                ]],
                ['How thorough is code review before merging?', [
                    'Code merges with little or no review.',
                    'Review happens but is often rubber-stamped.',
                    'Review catches most obvious issues.',
                    'Review is thorough and catches design-level issues too.',
                    'Review is a genuine quality gate the team trusts completely.',
                ]],
            ],
            'Cross-Team Dependencies' => [
                ['How smoothly does the team coordinate with other teams?', [
                    'Cross-team work causes constant surprises and delays.',
                    'Coordination happens but is often reactive.',
                    'Most cross-team work is planned, some friction remains.',
                    'Cross-team dependencies are proactively coordinated.',
                    'Cross-team coordination is seamless and trusted by all sides.',
                ]],
            ],
            'Incident Response' => [
                ['How effectively does the team respond to production incidents?', [
                    'Incidents cause panic with no clear process.',
                    'There is a process but it is rarely followed.',
                    'The team follows the process most of the time.',
                    'Incidents are handled calmly with clear ownership.',
                    'The team runs a tight, practiced incident response with good postmortems.',
                ]],
            ],
        ];

        foreach ($bank as $categoryName => $questions) {
            $category = QuestionCategory::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($questions as [$text, $tips]) {
                Question::updateOrCreate(
                    ['category_id' => $category->id, 'text' => $text],
                    [
                        'tip_0' => $tips[0], 'tip_1' => $tips[1], 'tip_2' => $tips[2],
                        'tip_3' => $tips[3], 'tip_4' => $tips[4],
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    // ------------------------------------------------------------------
    // Users
    // ------------------------------------------------------------------

    /** @return \Illuminate\Support\Collection<int, User> */
    private function seedUsers()
    {
        $plans = Plan::all()->keyBy('interval');
        $firstNames = ['Ava', 'Liam', 'Noah', 'Emma', 'Mia', 'Ethan', 'Sofia', 'Lucas', 'Zoe', 'Mason', 'Ella', 'Owen', 'Grace', 'Leo', 'Nina', 'Kai', 'Ruby', 'Theo', 'Ivy', 'Max'];
        $lastNames  = ['Chen', 'Patel', 'Nguyen', 'Garcia', 'Müller', 'Kowalski', 'Silva', 'Okafor', 'Andersson', 'Rossi'];

        $users = collect();
        $i = 0;
        foreach ($firstNames as $first) {
            $i++;
            $last = $lastNames[$i % count($lastNames)];
            $email = 'demo.' . Str::slug($first . '.' . $last) . '@example.com';

            $user = User::firstOrCreate(['email' => $email], [
                'name' => "{$first} {$last}",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subDays(rand(0, 30)),
            ]);

            if (!$user->hasRole('User') && !$user->hasRole('Admin')) {
                $user->assignRole('User');
            }

            // Spread across plans: ~50% free, ~30% monthly, ~20% yearly, a
            // few with a cancelled subscription (is_active=false) for realism.
            if (!$user->activeSubscription() && $plans->isNotEmpty()) {
                $roll = rand(1, 100);
                $plan = $roll <= 50 ? ($plans['free'] ?? $plans->first())
                    : ($roll <= 80 ? ($plans['monthly'] ?? $plans->first()) : ($plans['yearly'] ?? $plans->first()));

                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'starts_at' => now()->subMonths(rand(1, self::HISTORY_MONTHS)),
                    'ends_at' => null,
                    'is_active' => $roll > 95 ? false : true, // ~5% churned
                ]);
            }

            $users->push($user);
        }

        return $users;
    }

    // ------------------------------------------------------------------
    // Teams
    // ------------------------------------------------------------------

    /** @return \Illuminate\Support\Collection<int, Team> */
    private function seedTeams($users)
    {
        $domainIds = TeamDomain::pluck('id')->all();
        $frameworkIds = TeamFramework::pluck('id')->all();
        $memberRoles = TeamMemberRole::allOrdered();

        $teamNames = [
            'Platform Engineering', 'Growth Squad', 'Checkout Experience',
            'Data Platform', 'Mobile Guild', 'Design Systems',
        ];

        $teams = collect();
        $userPool = $users->values();

        foreach ($teamNames as $index => $name) {
            if (empty($domainIds) || empty($frameworkIds)) {
                $this->command?->warn('No team domains/frameworks found — run the base seeders first.');
                break;
            }

            $owner = $userPool[$index % $userPool->count()];

            $team = Team::firstOrCreate(['name' => $name], [
                'description' => "Demo team: {$name}",
                'team_domain_id' => $domainIds[array_rand($domainIds)],
                'team_framework_id' => $frameworkIds[array_rand($frameworkIds)],
                'owner_id' => $owner->id,
            ]);

            // 4-6 members per team, excluding the owner, with varied roles.
            $candidates = $userPool->reject(fn ($u) => $u->id === $owner->id)->shuffle();
            $memberCount = rand(4, 6);

            foreach ($candidates->take($memberCount) as $member) {
                if (!$team->members()->where('users.id', $member->id)->exists()) {
                    $role = $memberRoles->isNotEmpty()
                        ? $memberRoles->random()->slug
                        : 'member';
                    $team->members()->attach($member->id, ['role' => $role]);
                }
            }

            $teams->push($team->fresh());
        }

        return $teams;
    }

    // ------------------------------------------------------------------
    // Invitations
    // ------------------------------------------------------------------

    private function seedTeamInvitations($teams, $users): void
    {
        foreach ($teams as $team) {
            $nonMembers = $users->reject(
                fn ($u) => $u->id === $team->owner_id || $team->members->contains('id', $u->id)
            )->shuffle();

            // One pending, one expired, one already-declined-equivalent (just expired).
            foreach ($nonMembers->take(2) as $index => $candidate) {
                TeamInvitation::firstOrCreate([
                    'team_id' => $team->id,
                    'email' => $candidate->email,
                ], [
                    'role' => 'member',
                    'code' => Str::random(32),
                    'created_by' => $team->owner_id,
                    'expires_at' => $index === 0 ? now()->addDays(7) : now()->subDays(3),
                ]);
            }
        }
    }

    private function seedAdminInvites($users): void
    {
        $admin = User::role('Admin')->first() ?? $users->first();

        for ($i = 0; $i < 5; $i++) {
            Invite::firstOrCreate([
                'code' => 'DEMO' . strtoupper(Str::random(6)),
            ], [
                'created_by' => $admin->id,
                'max_uses' => 1,
                'times_used' => $i < 2 ? 1 : 0, // a couple already used
                'is_active' => true,
                'expires_at' => now()->addDays(30),
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Historical assessments — the actual trend-data engine
    // ------------------------------------------------------------------

    /**
     * One random active question per category (skipping categories with none),
     * so demo assessments — and their radar charts — cover every category
     * instead of clustering by chance in whichever 5-6 got picked at random.
     */
    private function questionPoolAcrossCategories()
    {
        return QuestionCategory::with(['questions' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->flatMap(fn ($category) => $category->questions->shuffle()->take(1))
            ->values();
    }

    private function seedHistoricalAssessmentsForTeam(Team $team, $allUsers): void
    {
        $alreadySeeded = Assessment::where('team_id', $team->id)
            ->where('title', 'like', '[Demo]%')
            ->exists();
        if ($alreadySeeded) {
            return;
        }

        $team->load('members');
        $memberIds = $team->members->pluck('id')->push($team->owner_id)->unique()->values();
        if ($memberIds->isEmpty()) {
            return;
        }

        $questionPool = $this->questionPoolAcrossCategories();
        if ($questionPool->isEmpty()) {
            return;
        }

        // Each team gets its own trajectory: a starting average and a
        // monthly drift, so trend charts show real, varied movement —
        // some teams improving, some flat, one or two declining.
        $startAvg = round(mt_rand(100, 220) / 100, 2); // 1.00 - 2.20
        $driftRoll = rand(1, 10);
        $monthlyDrift = $driftRoll <= 6 ? round(mt_rand(8, 20) / 100, 2)   // improving (60%)
            : ($driftRoll <= 9 ? round(mt_rand(-3, 3) / 100, 2)            // flat (30%)
            : round(mt_rand(-15, -5) / 100, 2));                          // declining (10%)

        // External evaluator candidates: users not on this team at all.
        $externalCandidates = $allUsers->reject(
            fn ($u) => $u->id === $team->owner_id || $memberIds->contains($u->id)
        )->shuffle();

        for ($monthsAgo = self::HISTORY_MONTHS; $monthsAgo >= 1; $monthsAgo--) {
            $periodDate = now()->copy()->subMonths($monthsAgo)->startOfMonth()->addDays(rand(1, 5));
            $target = max(0.3, min(3.9, $startAvg + ($monthlyDrift * (self::HISTORY_MONTHS - $monthsAgo))));

            $assessment = Assessment::create([
                'team_id' => $team->id,
                'created_by' => $team->owner_id,
                'title' => '[Demo] ' . $periodDate->format('F Y') . ' Maturity Check',
                'description' => 'Monthly team maturity check-in.',
                'status' => 'closed',
            ]);
            $assessment->forceFill(['created_at' => $periodDate, 'updated_at' => $periodDate])->saveQuietly();

            $order = 0;
            foreach ($questionPool as $question) {
                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'question_id' => $question->id,
                    'order' => ++$order,
                ])->forceFill(['created_at' => $periodDate, 'updated_at' => $periodDate])->saveQuietly();
            }

            // Occasionally exclude one member from this particular run (e.g. PTO).
            if ($memberIds->count() > 2 && rand(1, 5) === 1) {
                AssessmentExcludedMember::create([
                    'assessment_id' => $assessment->id,
                    'user_id' => $memberIds->random(),
                ]);
            }
            $excludedId = $assessment->excludedMembers->pluck('user_id')->first();

            $responseRows = [];
            $resultRows = [];

            foreach ($memberIds as $userId) {
                if ($userId === $excludedId) continue;

                $scores = [];
                foreach ($questionPool as $question) {
                    $score = max(0, min(4, (int) round($target + (mt_rand(-60, 60) / 100))));
                    $scores[] = $score;
                    $responseRows[] = [
                        'assessment_id' => $assessment->id,
                        'user_id' => $userId,
                        'question_id' => $question->id,
                        'score' => $score,
                        'comment' => null,
                        'respondent_type' => 'member',
                        'created_at' => $periodDate,
                        'updated_at' => $periodDate,
                    ];
                }
                $resultRows[] = [
                    'assessment_id' => $assessment->id,
                    'user_id' => $userId,
                    'overall_score' => round(array_sum($scores) / count($scores), 2),
                    'respondent_type' => 'member',
                    'created_at' => $periodDate,
                    'updated_at' => $periodDate,
                ];
            }

            // Roughly a third of historical assessments also had an external
            // evaluator who actually completed it.
            if (rand(1, 3) === 1 && $externalCandidates->isNotEmpty()) {
                $evaluator = $externalCandidates->first();
                AssessmentEvaluator::create([
                    'assessment_id' => $assessment->id,
                    'user_id' => $evaluator->id,
                    'invited_by' => $team->owner_id,
                    'status' => 'accepted',
                ])->forceFill(['created_at' => $periodDate, 'updated_at' => $periodDate])->saveQuietly();

                $scores = [];
                foreach ($questionPool as $question) {
                    $score = max(0, min(4, (int) round($target + (mt_rand(-60, 60) / 100))));
                    $scores[] = $score;
                    $responseRows[] = [
                        'assessment_id' => $assessment->id,
                        'user_id' => $evaluator->id,
                        'question_id' => $question->id,
                        'score' => $score,
                        'comment' => null,
                        'respondent_type' => 'external',
                        'created_at' => $periodDate,
                        'updated_at' => $periodDate,
                    ];
                }
                $resultRows[] = [
                    'assessment_id' => $assessment->id,
                    'user_id' => $evaluator->id,
                    'overall_score' => round(array_sum($scores) / count($scores), 2),
                    'respondent_type' => 'external',
                    'created_at' => $periodDate,
                    'updated_at' => $periodDate,
                ];
            }

            if (!empty($responseRows)) {
                DB::table('assessment_responses')->insert($responseRows);
            }
            if (!empty($resultRows)) {
                DB::table('assessment_results')->insert($resultRows);
            }
        }
    }

    private function seedCurrentAssessmentForTeam(Team $team): void
    {
        $alreadyHasCurrent = Assessment::where('team_id', $team->id)
            ->where('title', 'like', '[Demo] Current%')
            ->exists();
        if ($alreadyHasCurrent) {
            return;
        }

        $questionPool = $this->questionPoolAcrossCategories();
        if ($questionPool->isEmpty()) {
            return;
        }

        // One currently-active assessment (partially completed) and one draft,
        // so the app looks like an in-progress tool, not just a history archive.
        $active = Assessment::create([
            'team_id' => $team->id,
            'created_by' => $team->owner_id,
            'title' => '[Demo] Current Quarter Check-In',
            'description' => 'This quarter\'s in-progress maturity check.',
            'status' => 'active',
        ]);

        $order = 0;
        foreach ($questionPool as $question) {
            AssessmentQuestion::create([
                'assessment_id' => $active->id,
                'question_id' => $question->id,
                'order' => ++$order,
            ]);
        }

        $team->load('members');
        $memberIds = $team->members->pluck('id')->shuffle();
        // About half the team has completed it so far — realistic in-progress state.
        foreach ($memberIds->take((int) ceil($memberIds->count() / 2)) as $userId) {
            $scores = [];
            foreach ($questionPool as $question) {
                $score = rand(0, 4);
                $scores[] = $score;
                AssessmentResponse::create([
                    'assessment_id' => $active->id,
                    'user_id' => $userId,
                    'question_id' => $question->id,
                    'score' => $score,
                    'respondent_type' => 'member',
                ]);
            }
            AssessmentResult::create([
                'assessment_id' => $active->id,
                'user_id' => $userId,
                'overall_score' => round(array_sum($scores) / count($scores), 2),
                'respondent_type' => 'member',
            ]);
        }

        Assessment::create([
            'team_id' => $team->id,
            'created_by' => $team->owner_id,
            'title' => '[Demo] Next Quarter Planning Draft',
            'description' => 'Draft — not yet published.',
            'status' => 'draft',
        ]);
    }
}
