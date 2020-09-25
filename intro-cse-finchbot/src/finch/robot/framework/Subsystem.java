package finch.robot.framework;

/**
 * Created by yoseph on 5/10/2016.
 * A class that is used for any Subsystem, it allows for commands to require this subsystem and be able to stop
 * other commands that also use this subsystem.
 * This is useful when multiple commands are running and two that interfere with each other run.
 */
public abstract class Subsystem {

    private Command runningCommand = null;

    public Subsystem() {
        Scheduler.getInstance().addSubsystem(this);
    }

    /**
     * @return The current Command that requires this Subsystem.
     */
    public Command getRunningCommand() {
        return runningCommand;
    }

    /**
     * Tells this class that the following command is using it's subsystem.
     * <br/>
     * This is used to stop currently running commands when another command that require this class starts.
     *
     * @param com The command.
     */
    public void setRunningCommand(Command com) {
        runningCommand = com;
    }

    public abstract void init();

    /**
     * A cleanup method that gets called right before the robot turns off, or the program stops.
     */
    public abstract void end();
}
