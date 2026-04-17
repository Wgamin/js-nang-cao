const fs = require('fs');
const path = require('path');

const dir = 'd:/laragon/www/Javacrip_nang_cao/js-nang-cao/frontend/src/views/Admin';
const usersPath = path.join(dir, 'UsersView.vue');

const usersContent = fs.readFileSync(usersPath, 'utf8');

const styleRegex = /<style scoped>([\s\S]*?)<\/style>/;
const match = usersContent.match(styleRegex);

if (!match) {
    console.error('Could not find <style scoped> in UsersView.vue');
    process.exit(1);
}

let genericCSS = match[1];
// Make it generic so it works for all tables
genericCSS = genericCSS.replace(/\.user-table/g, '.data-table, .user-table');
genericCSS = genericCSS.replace(/\.user-full-name/g, '.user-full-name, .user-name');
genericCSS = genericCSS.replace(/\.user-email-text/g, '.user-email-text, .parent-sub, .class-info, .class-label');

const newStyleBlock = `<style scoped>\n${genericCSS.trim()}\n</style>`;

const targetFiles = [
    'StudentsView.vue',
    'TeachersView.vue',
    'SubjectsView.vue',
    'GuardiansView.vue',
    'ClassesView.vue',
    'ClassroomsView.vue',
    'TuitionView.vue',
    'EnrollmentsView.vue',
    'SchedulesView.vue',
    'AttendanceView.vue'
];

for (const file of targetFiles) {
    const filePath = path.join(dir, file);
    if (!fs.existsSync(filePath)) {
        console.warn('File not found:', filePath);
        continue;
    }
    const content = fs.readFileSync(filePath, 'utf8');
    
    // Replace its <style scoped> block
    if (styleRegex.test(content)) {
        const newContent = content.replace(styleRegex, newStyleBlock);
        fs.writeFileSync(filePath, newContent, 'utf8');
        console.log('Updated CSS for', file);
    } else {
        // If it just has <style>, replace that
        const plainStyleRegex = /<style>([\s\S]*?)<\/style>/;
        if (plainStyleRegex.test(content)) {
            const newContent = content.replace(plainStyleRegex, newStyleBlock);
            fs.writeFileSync(filePath, newContent, 'utf8');
            console.log('Updated CSS for', file);
        } else {
            console.warn('Could not find style block in', file);
        }
    }
}
console.log('Done.');
