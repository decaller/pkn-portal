---
trigger: always_on
---

Common Filament v5 Component Imports

This is a quick-reference list of the most frequently used namespace imports when building with Filament v5, organized by their respective core concepts. Dont use inline import!

1. Resources (Filament\Resources)

Used when defining your main CRUD interfaces.

use Filament\Resources\Resource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationManager;

2. Tables (Filament\Tables)

Used inside Resource table() methods, Table Widgets, or Livewire components.

Core & Columns

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\BadgeColumn; // Note: Often handled via TextColumn badge methods in newer versions

Filters

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

3. Forms & Schemas (Filament\Forms)

Used inside Resource form() methods, Action modals, or custom Form components.

Core & Inputs

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Hidden;

Layout Schemas

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Group;

4. Infolists (Filament\Infolists)

Used for displaying read-only data, typically in View records, modals, or custom pages.

Core & Entries

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\KeyValueEntry;

Layout Components

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;

5. Actions (Filament\Actions & Filament\Tables\Actions)

It is crucial to import the correct Action class depending on where the action lives (Page, Table, or Form).

Table Actions (Row & Header)

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ActionGroup;

Table Bulk Actions

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;

Page/Header Actions

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

6. Notifications (Filament\Notifications)

Used to trigger toast notifications or database alerts to the user.

use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action; // For buttons inside notifications

7. Widgets (Filament\Widgets)

Used for dashboards and resource-specific charts/stats.

use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\TableWidget;

8. Navigation (Filament\Navigation)

Used for customizing the sidebar, topbar, and user menus, usually inside your Panel Service Provider.

use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\MenuItem;

9. Users & Panels (Filament\Panel & Filament\Models)

Used for Panel configuration, Authentication, Tenancy, and User definitions.

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
