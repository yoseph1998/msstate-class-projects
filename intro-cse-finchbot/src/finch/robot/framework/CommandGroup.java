package finch.robot.framework;

import java.util.ArrayList;

/**
 * Created by yoseph on 5/10/2016.
 * A Command group class. Designed so that you can add commands to it using the addParallel or addSequential methods.
 * <br/>
 * When a command is added as a sequential command it will run that command, wait for it to finish, then run the next
 * command.
 * When a command is added as a parallel command it will run that command and run the next commands if they are
 * parallel.
 * The next sequential command will run after  all the current parallel commands are finished.
 * <br/>
 * For Example:
 * The following commands are added in their respective order.
 * <br/>
 * com1 - sequential
 * com2 - parallel
 * com3 - parallel
 * com4 - sequential
 * com5 - parallel
 * com6 - sequential
 * <br/>
 * com1 will run till it finishes, then com2 and com3 will run till they both finish, then com4 will run till it
 * finishes, then com5 will run till it finishes then com6 will run till it finishes.
 */
public class CommandGroup extends Command {
    private ArrayList<Command> commands = new ArrayList<Command>();
    private ArrayList<Sequence> sequences = new ArrayList<>();
    private int command0, command1;

    @Override
    public void init() {
        command0 = 0;
        command1 = 0;
    }

    @Override
    public void update() {
        int numFinished = 0;
        if (command1 < commands.size() - 1 && sequences.get(command1) == Sequence.PARALLEL
                && sequences.get(command1 + 1) == Sequence.PARALLEL) {
            command1++;
        }
        if (command1 >= commands.size())
            return;
        for (int i = command0; i <= command1; i++) {
            if (!commands.get(i).isRunning())
                commands.get(i).start();
            if (commands.get(i).hasEnded()) {
                numFinished++;
            }
        }
        if (numFinished == command1 - command0 + 1) {
            command1++;
            command0 = command1;
        }
    }

    /**
     * Will add a command to run in perallel with other commands before or after this command that are also parallel.
     *
     * @param com The command being added.
     */
    public void addParallel(Command com) {
        commands.add(com);
        sequences.add(Sequence.PARALLEL);
    }

    /**
     * Will add a command to run in sequence with
     *
     * @param com The command being added.
     */
    public void addSequential(Command com) {
        commands.add(com);
        sequences.add(Sequence.SEQUENTIAL);
    }

    @Override
    public boolean isFinished() {
        return command1 >= commands.size();
    }

    @Override
    protected void end() {
        for (int i = command0; i < command1; i++) {
            commands.get(i).stop();
        }
    }

    @Override
    protected void interrupt() {

    }

    private enum Sequence {
        PARALLEL, SEQUENTIAL;
    }


}
