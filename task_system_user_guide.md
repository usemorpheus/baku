# Baku Task System - User Guide

## How Users Access and Complete Tasks

The Baku task-driven积分 system allows users to earn points by completing specific tasks. Here's how users can access and use the system:

### 1. User Identification

The system identifies users through their Telegram user ID, which is captured when:
- Users interact with the @baku_news_bot via direct messages
- Users add the @baku_news_bot to a group
- Users send any message to a chat where @baku_news_bot is present

Once identified, the user ID is stored in the session, allowing the web interface to recognize the user.

### 2. Accessing Tasks

Users can access available tasks through two methods:

#### Method 1: Via Telegram Bot
1. Message @baku_news_bot with any command or text
2. Click the "Complete Tasks & Earn Points" button on the Baku homepage
3. The system will recognize your Telegram user ID and show personalized tasks

#### Method 2: Direct Web Access
1. Visit the Baku website
2. Click the "Complete Tasks & Earn Points" button
3. You must have recently interacted with the Telegram bot for this to work

### 3. Available Tasks

Currently available tasks include:

- **Add Bot to Group** (+100 points): Add @baku_news_bot to your Telegram group
- **Follow Twitter** (+100 points): Follow @Baku_builders on Twitter
- **Join Telegram Channel** (+50 points): Join the official Baku channel
- **Retweet Post** (+75 points): Retweet a post from @Baku_builders

### 4. Completing Tasks

1. Navigate to the Tasks page
2. View your personalized task list
3. Click "Claim Task" for any task you wish to complete
4. Follow the instructions for the specific task
5. For some tasks (like Twitter follow), you may need to provide additional information for verification

### 5. Tracking Progress

- Your total points are displayed prominently on the Tasks page
- Completed tasks are shown in the "Completed Tasks" section
- Pending tasks appear in the "Pending Tasks" section
- Points are automatically awarded when tasks are completed and verified

### 6. Security and Privacy

- Only authenticated Telegram users can access the task system
- All user data is stored securely
- Points are tracked individually and displayed on the public leaderboard
- User privacy is maintained while showing overall rankings

## Technical Details

The system works by:
1. Capturing Telegram user IDs through webhook interactions
2. Storing user sessions to maintain identity between Telegram and web
3. Tracking task completion in the user_tasks table
4. Calculating and displaying user-specific积分 on the points page