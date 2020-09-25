package finch.robot.framework;

import java.util.ArrayList;

/**
 * Created by yoseph on 5/10/2016.
 * A class used for making commands that will run on the Scheduler. They will take care of cleaning themselves up.
 */
public abstract class Command {

    private double timeout = -1;
    private double timeInit;

    private volatile boolean hasInitialized = false;

    private volatile boolean isRunning = false;
    private volatile boolean hasStarted = false;
    private ArrayList<Subsystem> subsystems = new ArrayList<Subsystem>();
    private volatile boolean hasInterrupted = false;

    private String name;

    public Command() {

    }

    public Command(String name) {
        this.name = name;
    }

    /**
     * Begins the command and adds it to the scheduler.
     */
    public synchronized void start() {
        if (!hasStarted) {
            Scheduler.getInstance().getCommandScheduler().addCommand(this);
            hasStarted = true;
        }
    }

    /**
     * Interrupts the command and stops it from updating. Will also call the interrupt method.
     * Removes the command from the scheduler.
     */
    public synchronized void stop() {
        if (!hasEnded()) {
            isRunning = false;
            hasInitialized = true;
            hasInterrupted = true;
        }
    }

    /**
     * A behind the scenes method for initializing the command.
     */
    protected void _init() {
        for (int i = 0; i < subsystems.size(); i++) {
            if (subsystems.get(i).getRunningCommand() != null)
                subsystems.get(i).getRunningCommand().stop();
            subsystems.get(i).setRunningCommand(this);
        }
        timeInit = System.nanoTime() / 1000000000.0;
        init();
    }

    /**
     * The execution method for the scheduler, should not be called by anything else.
     */
    protected void _update() {
        if (!hasInitialized) {
            _init();
            hasInitialized = true;
            isRunning = true;
        }
        if (isFinished()) {
            _end();
        } else if (isTimedOut()) {
            _interrupt();
        } else {
            update();
        }
    }

    /**
     * A behind the scenes method for handling ending.
     */
    protected void _end() {
        isRunning = false;
        for (int i = 0; i < subsystems.size(); i++) {
            subsystems.get(i).setRunningCommand(null);
        }
        end();
    }

    /**
     * A behind the scenes method for handling interrupts.
     */
    protected void _interrupt() {
        isRunning = false;
        for (int i = 0; i < subsystems.size(); i++) {
            subsystems.get(i).setRunningCommand(null);
        }
        interrupt();
    }

    /**
     * Resets all fields and variables to their initial state.
     * This allows this instance of the command to be started after stopped and safely ended.
     */
    protected void reset() {
        hasInterrupted = false;
        hasStarted = false;
        hasInitialized = false;
        isRunning = false;
    }

    /**
     * Adds the given Subsystem to the listed of required subsystems.
     *
     * @param subsystem The required subsystem.
     */
    protected void requires(Subsystem subsystem) {
        subsystems.add(subsystem);
    }

    /**
     * If false the command could have not started or ended.
     * If true the command must currently be running.
     *
     * @return Weather this command is running or not.
     */
    public synchronized boolean isRunning() {
        return isRunning;
    }

    /**
     * @return Weather this command has ended, ie finished running.
     */
    protected boolean hasEnded() {
        return hasInitialized && !isRunning();
    }

    protected boolean hasInterrupted() {
        return hasInterrupted;
    }

    /**
     * Called when the command starts once.
     */
    protected abstract void init();

    /**
     * Called every iteration while the command is running.
     */
    protected abstract void update();

    /**
     * If it is finished the command will call end and remove it's self from the scheduler.
     *
     * @return Weather the command is finished or not.
     */
    protected abstract boolean isFinished();

    /**
     * The cleanup method for when the command ends.
     */
    protected abstract void end();

    /**
     * Is called when the command is interrupted.
     */
    protected abstract void interrupt();

    /**
     * @return The amount of time this command has been running in seconds.
     */
    public double getPassedTime() {
        return System.nanoTime() / 1000000000.0 - timeInit;
    }

    /**
     * @return Weather the command has timed out.
     */
    public boolean isTimedOut() {
        return getPassedTime() > timeout && timeout >= 0;
    }

    /**
     * Sets the timeout for the command.
     *
     * @param timeout
     */
    public void setTimeout(double timeout) {
        this.timeout = timeout;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }
}
