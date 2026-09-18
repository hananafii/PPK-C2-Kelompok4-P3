<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'Demo User',
            'email' => 'user@jara.com',
            'password' => bcrypt('password'),
        ]);

        $taskList = TaskList::first() ?? TaskList::create([
            'user_id' => $user->id,
            'name' => 'General Tasks',
            'description' => 'Main task list for JARA application',
        ]);

        $budi = $user;
        $siti = $user;
        $andi = $user;
        $websiteList = $taskList;
        $mobileList = $taskList;
        $personalList = $taskList;

        if ($websiteList && $budi) {
            Task::firstOrCreate(
                ['task_list_id' => $websiteList->id, 'title' => 'Design UI/UX Mockups with Figma'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $siti->id,
                    'description' => 'Create high-fidelity wireframes and prototype components for all pages.',
                    'priority' => 'High',
                    'status' => 'Completed',
                    'deadline' => Carbon::now()->subDays(2)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $websiteList->id, 'title' => 'Setup Database Schema & Migrations'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $budi->id,
                    'description' => 'Create migrations for users, task_lists, tasks, and task_list_user pivot table in MySQL.',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'deadline' => Carbon::now()->addDays(2)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $websiteList->id, 'title' => 'Implement Authentication & RBAC'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $budi->id,
                    'description' => 'Build login, register, password hashing, and role middleware for admin and user roles.',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'deadline' => Carbon::now()->addDays(3)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $websiteList->id, 'title' => 'Integrate Responsive Dashboard'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $andi->id,
                    'description' => 'Develop modern dashboard with task completion statistics, cards, and recent activity.',
                    'priority' => 'Medium',
                    'status' => 'Pending',
                    'deadline' => Carbon::now()->addDays(5)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $websiteList->id, 'title' => 'Automated Unit & Feature Testing'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $siti->id,
                    'description' => 'Write comprehensive Pest/PHPUnit tests covering authentication and task management.',
                    'priority' => 'Medium',
                    'status' => 'Pending',
                    'deadline' => Carbon::now()->addDays(7)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $websiteList->id, 'title' => 'Deploy to Production Server'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $andi->id,
                    'description' => 'Configure environment, run migrations, and test live deployment.',
                    'priority' => 'Low',
                    'status' => 'Pending',
                    'deadline' => Carbon::now()->addDays(14)->format('Y-m-d'),
                ]
            );
        }

        if ($mobileList && $siti && $budi) {
            Task::firstOrCreate(
                ['task_list_id' => $mobileList->id, 'title' => 'Design Onboarding Flow'],
                [
                    'created_by' => $siti->id,
                    'assigned_to' => $siti->id,
                    'description' => 'Create initial splash and onboarding walkthrough slides for mobile users.',
                    'priority' => 'Medium',
                    'status' => 'Completed',
                    'deadline' => Carbon::now()->subDays(1)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $mobileList->id, 'title' => 'Build RESTful API Endpoints'],
                [
                    'created_by' => $siti->id,
                    'assigned_to' => $budi->id,
                    'description' => 'Implement JSON API endpoints for tasks, lists, and synchronization.',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'deadline' => Carbon::now()->addDays(4)->format('Y-m-d'),
                ]
            );

            Task::firstOrCreate(
                ['task_list_id' => $mobileList->id, 'title' => 'Integrate Push Notifications'],
                [
                    'created_by' => $siti->id,
                    'assigned_to' => $siti->id,
                    'description' => 'Setup FCM integration for reminder alerts when deadlines approach.',
                    'priority' => 'Low',
                    'status' => 'Pending',
                    'deadline' => Carbon::now()->addDays(10)->format('Y-m-d'),
                ]
            );
        }

        if ($personalList && $budi) {
            Task::firstOrCreate(
                ['task_list_id' => $personalList->id, 'title' => 'Complete Laravel Deep Dive'],
                [
                    'created_by' => $budi->id,
                    'assigned_to' => $budi->id,
                    'description' => 'Study advanced Eloquent relationships, query optimization, and architectural patterns.',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'deadline' => Carbon::now()->addDays(6)->format('Y-m-d'),
                ]
            );
        }
    }
}
