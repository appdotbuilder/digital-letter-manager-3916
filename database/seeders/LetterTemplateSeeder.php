<?php

namespace Database\Seeders;

use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class LetterTemplateSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $templates = [
            [
                'name' => 'Official Business Letter',
                'description' => 'Standard format for official business communications',
                'content' => '<div style="margin-bottom: 20px;">
    <p><strong>{{DATE}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p>{{RECIPIENT_NAME}}<br>
    {{RECIPIENT_ADDRESS}}</p>
</div>

<div style="margin-bottom: 20px;">
    <p><strong>Subject: {{SUBJECT}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p>Dear {{RECIPIENT_NAME}},</p>
</div>

<div style="margin-bottom: 20px; line-height: 1.6;">
    <p>{{LETTER_BODY}}</p>
</div>

<div style="margin-bottom: 20px;">
    <p>Thank you for your attention to this matter.</p>
</div>

<div>
    <p>Sincerely,<br><br>
    {{SENDER_NAME}}<br>
    {{SENDER_TITLE}}<br>
    {{ORGANIZATION_NAME}}</p>
</div>',
                'placeholders' => [
                    'DATE' => 'Current date',
                    'RECIPIENT_NAME' => 'Name of the letter recipient',
                    'RECIPIENT_ADDRESS' => 'Full address of the recipient',
                    'SUBJECT' => 'Letter subject line',
                    'LETTER_BODY' => 'Main content of the letter',
                    'SENDER_NAME' => 'Name of the person sending the letter',
                    'SENDER_TITLE' => 'Job title of the sender',
                    'ORGANIZATION_NAME' => 'Name of the organization',
                ],
            ],
            [
                'name' => 'Meeting Request',
                'description' => 'Template for requesting meetings with external parties',
                'content' => '<div style="margin-bottom: 20px;">
    <p><strong>{{DATE}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p>{{RECIPIENT_NAME}}<br>
    {{RECIPIENT_TITLE}}<br>
    {{RECIPIENT_ORGANIZATION}}<br>
    {{RECIPIENT_ADDRESS}}</p>
</div>

<div style="margin-bottom: 20px;">
    <p><strong>Subject: Request for Meeting - {{MEETING_TOPIC}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p>Dear {{RECIPIENT_NAME}},</p>
</div>

<div style="margin-bottom: 20px; line-height: 1.6;">
    <p>I hope this letter finds you well. I am writing to request a meeting to discuss {{MEETING_TOPIC}}.</p>
    
    <p><strong>Proposed Details:</strong></p>
    <ul>
        <li><strong>Date:</strong> {{PROPOSED_DATE}}</li>
        <li><strong>Time:</strong> {{PROPOSED_TIME}}</li>
        <li><strong>Duration:</strong> {{ESTIMATED_DURATION}}</li>
        <li><strong>Location:</strong> {{MEETING_LOCATION}}</li>
    </ul>
    
    <p>The purpose of this meeting would be to {{MEETING_PURPOSE}}. I believe this discussion would be mutually beneficial and look forward to your positive response.</p>
</div>

<div style="margin-bottom: 20px;">
    <p>Please let me know if the proposed time works for you, or suggest an alternative that better fits your schedule.</p>
</div>

<div>
    <p>Best regards,<br><br>
    {{SENDER_NAME}}<br>
    {{SENDER_TITLE}}<br>
    {{ORGANIZATION_NAME}}<br>
    {{CONTACT_INFORMATION}}</p>
</div>',
                'placeholders' => [
                    'DATE' => 'Current date',
                    'RECIPIENT_NAME' => 'Name of the meeting invitee',
                    'RECIPIENT_TITLE' => 'Job title of the invitee',
                    'RECIPIENT_ORGANIZATION' => 'Organization of the invitee',
                    'RECIPIENT_ADDRESS' => 'Address of the invitee',
                    'MEETING_TOPIC' => 'Main topic of the meeting',
                    'PROPOSED_DATE' => 'Suggested meeting date',
                    'PROPOSED_TIME' => 'Suggested meeting time',
                    'ESTIMATED_DURATION' => 'How long the meeting will take',
                    'MEETING_LOCATION' => 'Where the meeting will be held',
                    'MEETING_PURPOSE' => 'Detailed purpose of the meeting',
                    'SENDER_NAME' => 'Name of the person requesting the meeting',
                    'SENDER_TITLE' => 'Job title of the sender',
                    'ORGANIZATION_NAME' => 'Name of the organization',
                    'CONTACT_INFORMATION' => 'Contact details for follow-up',
                ],
            ],
            [
                'name' => 'Contract Proposal',
                'description' => 'Template for proposing business contracts or agreements',
                'content' => '<div style="margin-bottom: 20px;">
    <p><strong>{{DATE}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p>{{RECIPIENT_NAME}}<br>
    {{RECIPIENT_TITLE}}<br>
    {{RECIPIENT_ORGANIZATION}}<br>
    {{RECIPIENT_ADDRESS}}</p>
</div>

<div style="margin-bottom: 20px;">
    <p><strong>Subject: Business Proposal - {{CONTRACT_SUBJECT}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p>Dear {{RECIPIENT_NAME}},</p>
</div>

<div style="margin-bottom: 20px; line-height: 1.6;">
    <p>We are pleased to submit this proposal for {{CONTRACT_SUBJECT}}. After careful consideration of your requirements, we believe we can provide an excellent solution that meets your needs.</p>
    
    <p><strong>Proposal Overview:</strong></p>
    <ul>
        <li><strong>Project Scope:</strong> {{PROJECT_SCOPE}}</li>
        <li><strong>Proposed Timeline:</strong> {{TIMELINE}}</li>
        <li><strong>Total Investment:</strong> {{TOTAL_COST}}</li>
        <li><strong>Payment Terms:</strong> {{PAYMENT_TERMS}}</li>
    </ul>
    
    <p><strong>Key Deliverables:</strong></p>
    <p>{{DELIVERABLES}}</p>
    
    <p>We are confident that this partnership will be mutually beneficial and look forward to working with you on this exciting project.</p>
</div>

<div style="margin-bottom: 20px;">
    <p>Please review this proposal and let us know if you have any questions or would like to discuss any aspect in more detail. We are available to meet at your convenience.</p>
</div>

<div>
    <p>Sincerely,<br><br>
    {{SENDER_NAME}}<br>
    {{SENDER_TITLE}}<br>
    {{ORGANIZATION_NAME}}<br>
    {{CONTACT_INFORMATION}}</p>
</div>',
                'placeholders' => [
                    'DATE' => 'Current date',
                    'RECIPIENT_NAME' => 'Name of the proposal recipient',
                    'RECIPIENT_TITLE' => 'Job title of the recipient',
                    'RECIPIENT_ORGANIZATION' => 'Organization of the recipient',
                    'RECIPIENT_ADDRESS' => 'Address of the recipient',
                    'CONTRACT_SUBJECT' => 'Main subject of the contract',
                    'PROJECT_SCOPE' => 'Overview of what will be delivered',
                    'TIMELINE' => 'Project completion timeline',
                    'TOTAL_COST' => 'Total cost of the project',
                    'PAYMENT_TERMS' => 'Payment schedule and terms',
                    'DELIVERABLES' => 'Detailed list of deliverables',
                    'SENDER_NAME' => 'Name of the proposal sender',
                    'SENDER_TITLE' => 'Job title of the sender',
                    'ORGANIZATION_NAME' => 'Name of the organization',
                    'CONTACT_INFORMATION' => 'Contact details for follow-up',
                ],
            ],
            [
                'name' => 'Policy Notification',
                'description' => 'Template for communicating policy changes or updates',
                'content' => '<div style="margin-bottom: 20px;">
    <p><strong>{{DATE}}</strong></p>
</div>

<div style="margin-bottom: 20px;">
    <p><strong>MEMORANDUM</strong></p>
    <p><strong>TO:</strong> {{RECIPIENT_GROUP}}<br>
    <strong>FROM:</strong> {{SENDER_NAME}}, {{SENDER_TITLE}}<br>
    <strong>DATE:</strong> {{DATE}}<br>
    <strong>RE:</strong> {{POLICY_SUBJECT}}</p>
</div>

<div style="margin-bottom: 20px; line-height: 1.6;">
    <p>This memorandum is to inform you of {{POLICY_TYPE}} regarding {{POLICY_SUBJECT}}.</p>
    
    <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
    
    <p><strong>Policy Details:</strong></p>
    <p>{{POLICY_DETAILS}}</p>
    
    <p><strong>Reason for Change:</strong></p>
    <p>{{POLICY_REASON}}</p>
    
    <p><strong>Action Required:</strong></p>
    <p>{{ACTION_REQUIRED}}</p>
</div>

<div style="margin-bottom: 20px;">
    <p>If you have any questions or concerns regarding this {{POLICY_TYPE}}, please do not hesitate to contact {{CONTACT_PERSON}} at {{CONTACT_DETAILS}}.</p>
</div>

<div>
    <p>Thank you for your attention and cooperation.<br><br>
    {{SENDER_NAME}}<br>
    {{SENDER_TITLE}}<br>
    {{ORGANIZATION_NAME}}</p>
</div>',
                'placeholders' => [
                    'DATE' => 'Current date',
                    'RECIPIENT_GROUP' => 'Who this policy affects (e.g., All Staff, Department X)',
                    'SENDER_NAME' => 'Name of the policy issuer',
                    'SENDER_TITLE' => 'Job title of the sender',
                    'POLICY_SUBJECT' => 'Main subject of the policy',
                    'POLICY_TYPE' => 'Type of change (new policy, policy update, etc.)',
                    'EFFECTIVE_DATE' => 'When the policy takes effect',
                    'POLICY_DETAILS' => 'Detailed description of the policy',
                    'POLICY_REASON' => 'Why this policy is being implemented',
                    'ACTION_REQUIRED' => 'What recipients need to do',
                    'CONTACT_PERSON' => 'Who to contact for questions',
                    'CONTACT_DETAILS' => 'Contact information',
                    'ORGANIZATION_NAME' => 'Name of the organization',
                ],
            ],
        ];

        foreach ($templates as $template) {
            LetterTemplate::create([
                'name' => $template['name'],
                'description' => $template['description'],
                'content' => $template['content'],
                'placeholders' => $template['placeholders'],
                'is_active' => true,
                'created_by' => $admin->id,
            ]);
        }
    }
}